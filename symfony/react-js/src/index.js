import React from 'react'
import ReactDOM from 'react-dom'
import { applyMiddleware, createStore } from 'redux' // control how the application state changes in response to actions
import { createBrowserHistory } from 'history'; // easily manage the browser session history
import { Provider } from 'react-redux'
import { ConnectedRouter } from 'react-router-redux'
import { Route } from 'react-router'
import App from './components/App'
import reducer from './reducer';
import thunkMiddleware from 'redux-thunk';
import { tokenMiddleware } from './middleware';

const store = createStore(
    reducer,
    applyMiddleware(thunkMiddleware, tokenMiddleware)
);
const history = createBrowserHistory();

ReactDOM.render((
    <Provider store={store}>
        <ConnectedRouter history={history}>
            <Route path="/" component={App} />
        </ConnectedRouter>
    </Provider>
), document.getElementById('root'));