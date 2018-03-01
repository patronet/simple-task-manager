import update from 'react-addons-update';

export default (state = [], action) => {
    if (action.type == 'SET_SIDEBAR_MINIMIZED') {
        // TODO
        return update(state, {side: {minimized: {$set: action.minimized}}});
    } else if (action.type == 'MOVE_TO_PAGE') {
        return update(state, {
            major: {
                pageType: {$set: action.pageType},
                pageProperties: {$set: action.pageProperties},
            }
        });
    } else {
        return state;
    }
}
