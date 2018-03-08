import update from 'react-addons-update';

export default (state = [], action) => {
    if (action.type == 'MOVE_TO_PAGE') {
        return update(state, {
            major: {
                pageType: {$set: action.pageType},
                pageProperties: {$set: action.pageProperties},
            }
        });
    } else if (action.type == 'SET_SIDEBAR_MINIMIZED') {
        // TODO
        return update(state, {side: {minimized: {$set: action.minimized}}});
    } else if (action.type == 'SHOW_MODAL') {
        return update(state, {
            modal: {
                isOpen: {$set: true},
                modalType: {$set: action.modalType},
                title: {$set: action.title},
                content: {$set: action.content},
                action: {$set: action.action},
                actionButtonText: {$set: action.actionButtonText},
                abortable: {$set: action.abortable},
            }
        });
    } else if (action.type == 'CLOSE_MODAL') {
        return update(state, {
            modal: {
                isOpen: {$set: false},
            }
        });
    } else {
        return state;
    }
}
