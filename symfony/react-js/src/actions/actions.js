import { requests } from "../agent";
import { BLOG_POST_LIST_REQUEST, BLOG_POST_LIST_RECEIVED, BLOG_POST_LIST_ERROR, BLOG_POST_LIST_ADD, BLOG_POST_REQUEST, BLOG_POST_RECEIVED, BLOG_POST_ERROR, BLOG_POST_UNLOAD, COMMENT_LIST_REQUEST, COMMENT_LIST_RECEIVED, COMMENT_LIST_ERROR, COMMENT_LIST_UNLOAD, COMMENT_ADDED, USER_LOGIN_SUCCESS, USER_PROFILE_REQUEST, USER_PROFILE_ERROR, USER_PROFILE_RECEIVED, USER_SET_ID, USER_LOGOUT, BLOG_POST_LIST_SET_PAGE } from './constants'
import { SubmissionError } from "redux-form";
import { parseApiErrors } from "../apiUtils";

export const blogPostListRequest = () => ({
    type: BLOG_POST_LIST_REQUEST,
});

export const blogPostListReceived = (data) => ({
    type: BLOG_POST_LIST_RECEIVED,
    data
});

export const blogPostListError = (error) => ({
    type: BLOG_POST_LIST_ERROR,
    error
});

export const blogPostListSetPage = (page) => ({
    type: BLOG_POST_LIST_SET_PAGE,
    page
});

export const blogPostListFetch = (page = 1) => {
    return (dispatch) => {
        dispatch(blogPostListRequest());
        return requests.get(`/blog_posts?_page=${page}`)
            .then(response => dispatch(blogPostListReceived(response)))
            .catch(error => dispatch(blogPostListError(error)));
    }
};

export const blogPostRequest = () => ({
    type: BLOG_POST_REQUEST,
});

export const blogPostReceived = (data) => ({
    type: BLOG_POST_RECEIVED,
    data
});

export const blogPostError = (error) => ({
    type: BLOG_POST_ERROR,
    error
});

export const blogPostUnload = (error) => ({
    type: BLOG_POST_UNLOAD,
    error
});

export const blogPostFetch = (id) => {
    return (dispatch) => {
        dispatch(blogPostRequest());
        return requests.get(`/blog_posts/${id}`)
            .then(response => dispatch(blogPostReceived(response)))
            .catch(error => dispatch(blogPostError(error)));
    }
};

export const commentListRequest = () => ({
    type: COMMENT_LIST_REQUEST,
});

export const commentListReceived = (data) => ({
    type: COMMENT_LIST_RECEIVED,
    data
});

export const commentListError = (error) => ({
    type: COMMENT_LIST_ERROR,
    error
});

export const commentListUnload = (error) => ({
    type: COMMENT_LIST_UNLOAD,
    error
});

export const commentListFetch = (id) => {
    return (dispatch) => {
        dispatch(commentListRequest());
        return requests.get(`/blog_posts/${id}/comments`)
            .then(response => dispatch(commentListReceived(response)))
            .catch(error => dispatch(commentListError(error)));
    }
};

export const commentAdded = (comment) => ({
    type: COMMENT_ADDED,
    comment
});

export const commentAdd = (comment, blogPostId) => {
    return (dispatch) => {
        return requests.post('/comments', {
            content: comment,
            blogPost: `/api/blog_posts/${blogPostId}`
        }).then(
            response => dispatch(commentAdded(response))
        ).catch((error) => {
            if (401 === error.response.status) {
                return dispatch(userLogout());
            }

            throw new SubmissionError(parseApiErrors(error))
        })
    }
};

export const userLoginSuccess = (token, userId) => {
    return {
        type: USER_LOGIN_SUCCESS,
        token,
        userId
    }
};

export const userLoginAttempt = (username, password) => {
    return (dispatch) => {
        return requests.post('/login_check', { username, password }, false).then(
            response => dispatch(userLoginSuccess(response.token, response.id))
        ).catch(() => {
            throw new SubmissionError({
                _error: 'Username or password is invalid'
            })
        });
    }
};

export const userLogout = () => {
    return {
        type: USER_LOGOUT
    }
}

export const userSetId = (userId) => {
    return {
        type: USER_SET_ID,
        userId
    }
};

export const userProfileRequest = () => {
    return {
        type: USER_PROFILE_REQUEST
    }
};


export const userProfileError = (userId) => {
    return {
        type: USER_PROFILE_ERROR,
        userId
    }
};

export const userProfileReceived = (userId, userData) => {
    return {
        type: USER_PROFILE_RECEIVED,
        userData,
        userId
    }
};

export const userProfileFetch = (userId) => {
    return (dispatch) => {
        dispatch(userProfileRequest());
        return requests.get(`/users/${userId}`, true).then(
            response => dispatch(userProfileReceived(userId, response))
        ).catch(() => dispatch(userProfileError(userId)))
    }
};

export const blogPostAdd = () => ({
    type: BLOG_POST_LIST_ADD,
    data: {
        id: Math.floor(Math.random() * 100 + 3),
        title: 'A newly added blog post'
    }
});