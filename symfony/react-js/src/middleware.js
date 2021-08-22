import { USER_LOGIN_SUCCESS } from "./actions/constants";
import { requests } from "./agent";

// Middleware is a curied function, a curied function is a function that return other function
export const tokenMiddleware = store => next => action => {
    switch (action.type) {
        case USER_LOGIN_SUCCESS:
            window.localStorage.setItem('jwtToken', action.token);
            window.localStorage.setItem('userId', action.userId);
            requests.setToken(action.token);
            break;
        default:

    }

    next(action);
};