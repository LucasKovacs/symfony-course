import { COMMENT_LIST_REQUEST, COMMENT_LIST_RECEIVED, COMMENT_LIST_ERROR, COMMENT_LIST_UNLOAD, COMMENT_ADDED } from '../actions/constants'

export default (state = {
    commentList: null,
    isFetching: false
}, action) => {
    switch (action.type) {
        case COMMENT_LIST_REQUEST:
            return {
                ...state,
                isFetching: true,
            };
        case COMMENT_LIST_RECEIVED:
            return {
                ...state,
                isFetching: false,
                commentList: action.data['hydra:member']
            };
        case COMMENT_ADDED:
            return {
                ... state,
                commentList: [action.comment, ...state.commentList]
            };
        case COMMENT_LIST_ERROR:
        case COMMENT_LIST_UNLOAD:
            return {
                ...state,
                isFetching: false,
                commentList: null
            };
        default:
            return state;
    }
}