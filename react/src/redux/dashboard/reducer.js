import update from 'react-addons-update';

export default (state = [], action) => {
    if (action.type == 'REQUEST_DASHBOARD') {
        return update(state, {isFetching: {$set: true}});
    } else if (action.type == 'RECEIVE_DASHBOARD') {
        return update(state, {isFetching: {$set: false}, activeProjectCount: {$set: action.activeProjectCount}});
    } else {
        return state;
    }
}
