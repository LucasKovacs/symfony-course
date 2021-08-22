import React from 'react';
import { commentListFetch, commentListUnload } from "../actions/actions";
import { connect } from "react-redux";
import { CommentList } from "./CommentList";
import { Spinner } from "./Spinner";

const mapeStateToProps = state => ({
    ...state.commentList
});

const mapDispatchToProps = {
    commentListFetch,
    commentListUnload
};

class CommentListContainer extends React.Component {
    componentDidMount() {
        this.props.commentListFetch(this.props.blogPostId);
    }

    componentWillUnmount() {
        this.props.commentListUnload();
    }

    render() {
        const { isFetching, commentList } = this.props;

        if (isFetching) {
            return (<Spinner />);
        }

        return (
            <div>
                <CommentList commentList={commentList} />
            </div>
        )
    }
}

export default connect(mapeStateToProps, mapDispatchToProps)(CommentListContainer);