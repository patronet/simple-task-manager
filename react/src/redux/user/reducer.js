import update from 'react-addons-update';

export default (state = {}, action = null) => {
    if (action.type == 'SET_LOGIN') {
        return update(state, {logInData: {$set: {username: action.username, password: action.password}}});
    } else if (action.type == 'ACCEPT_LOGIN') {
        return update(state, {isLoggedIn: {$set: true}});
    } else if (action.type == 'CLEAR_LOGIN') {
        return update(state, {isLoggedIn: {$set: false}});
    } else if (action.type == 'LOG_OUT') {
        return update(state, {isLoggedIn: {$set: false}, logInData: {$set: null}, userData: {$set: null}});
    } else {
        return state;
    }
}
