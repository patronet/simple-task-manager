


// XXX this is a temporary solution for rounting
export function moveToPage(dispatch, pageType, pageProperties) {
    dispatch({
        type: 'MOVE_TO_PAGE',
        pageType,
        pageProperties
    });
}

export function setSidebarMinimized(dispatch, minimized) {
    dispatch({
        type: 'SET_SIDEBAR_MINIMIZED',
        minimized: minimized
    });
}

export function showMessage(dispatch, content, title = "Üzenet", modalType = "info") {
    dispatch({
        type: 'SHOW_MODAL',
        modalType, title, content,
    });
}

export function closeModal(dispatch) {
    dispatch({
        type: 'CLOSE_MODAL',
    });
}
