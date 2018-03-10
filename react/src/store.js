import userReducer from './redux/user/reducer'
import frameReducer from './redux/frame/reducer'
import dashboardReducer from './redux/dashboard/reducer'
import taskBoardReducer from './redux/taskBoard/reducer'
import projectsReducer from './redux/projects/reducer'
import { combineReducers, createStore } from 'redux'
import testData from './testData'


export default createStore(combineReducers({
    user: userReducer,
    frame: frameReducer,
    dashboard: dashboardReducer,
    taskBoard: taskBoardReducer,
    projects: projectsReducer,
}), testData);
