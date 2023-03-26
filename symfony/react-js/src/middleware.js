import { USER_LOGIN_SUCCESS, USER_LOGOUT } from "./actions/constants";
import { requests } from "./agent";

// Middleware is a curied function, a curied function is a function that return other function
export const tokenMiddleware = store => next => action => {
    switch (action.type) {
        case USER_LOGIN_SUCCESS:
            window.localStorage.setItem('jwtToken', action.token);
            window.localStorage.setItem('userId', action.userId);
            requests.setToken(action.token);
            break;
        case USER_LOGOUT:
            window.localStorage.removeItem('jwtToken');
            window.localStorage.removeItem('userId');
            requests.setToken(null);
            break;
        default:

    }

    next(action);
};