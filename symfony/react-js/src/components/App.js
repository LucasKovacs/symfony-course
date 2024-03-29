import React from 'react';
import {Route, Switch} from "react-router";
import LoginForm from "./LoginForm";
import BlogPostListContainer from "./BlogPostListContainer";
import Header from "./Header";
import BlogPostContainer from "./BlogPostContainer";
import {requests} from "../agent";
import {connect} from "react-redux";
import {userLogout, userProfileFetch, userSetId} from "../actions/actions";
import RegistrationContainer from "./RegistrationContainer";
import BlogPostForm from "./BlogPostForm";

const mapStateToProps = state => ({
    ...state.auth
});

const mapDispatchToProps = {
    userProfileFetch, userSetId, userLogout
};

class App extends React.Component {
    constructor(props) {
        super(props);

        const token = window.localStorage.getItem('jwtToken');

        if (token) {
            requests.setToken(token);
        }
    }

    componentDidMount() {
        const userId = window.localStorage.getItem('userId');
        const { userSetId } = this.props;

        if (userId) {
            userSetId(userId);
        }
    }

    componentDidUpdate(prevProps) {
        const { userId, userData, userProfileFetch } = this.props;

        if (prevProps.userId !== userId && userId !== null && userData === null) {
            userProfileFetch(userId);
        }
    }

    render() {
        const { isAuthenticated, userData, userLogout } = this.props;

        return (
            <div>
                <Header isAuthenticated={isAuthenticated} userData={userData} logout={userLogout} />
                <Switch>
                    <Route path="/login" component={LoginForm} />
                    <Route path="/blog-post-form" component={BlogPostForm}/>
                    <Route path="/blog-post/:id" component={BlogPostContainer} />
                    <Route path="/register" component={RegistrationContainer} />
                    <Route path="/:page?" component={BlogPostListContainer} /> {/* Must be the last rule or we need to add an "exact" parameter */}
                </Switch>
            </div >
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(App);