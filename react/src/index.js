import React from 'react';
import ReactDOM from 'react-dom';
import { combineReducers, createStore } from 'redux'
import { Provider } from 'react-redux'
import App from './App';
import frameReducer from './redux/frame/reducer'
import dashboardReducer from './redux/dashboard/reducer'
import projectsReducer from './redux/projects/reducer'
import testData from './testData'

import './index.css';

let store = createStore(combineReducers({
    frame: frameReducer,
    dashboard: dashboardReducer,
    projects: projectsReducer,
}), testData);

ReactDOM.render(
    <Provider store={store}>
        <App />
    </Provider>,
document.getElementById('root'));
