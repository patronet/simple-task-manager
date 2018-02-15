import React from 'react';
import ReactDOM from 'react-dom';
import { combineReducers, createStore } from 'redux'
import { Provider } from 'react-redux'
import App from './App';
import projectsReducer from './redux/projects/reducer'
import sprintsReducer from './redux/sprints/reducer'
import testData from './testData'

import './index.css';

let store = createStore(combineReducers({
    projects: projectsReducer,
    sprints: sprintsReducer
}), testData);

ReactDOM.render(
    <Provider store={store}>
        <App />
    </Provider>,
document.getElementById('root'));
