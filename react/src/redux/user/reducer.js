import update from 'react-addons-update';

export default (state = [], action) => {
    if (action.type == 'LOGGED_IN') {
        // TODO
        return update(state, {isLoggedIn: {$set: true}});
    } else if (action.type == 'CLEAR_LOGIN') {
        return update(state, {isLoggedIn: {$set: false}});
    } else {
        return state;
    }
}
