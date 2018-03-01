
export function setSidebarMinimized(dispatch, minimized) {
    dispatch({
        type: 'SET_SIDEBAR_MINIMIZED',
        minimized: minimized
    });
}

// XXX this is a temporary solution for rounting
export function moveToPage(dispatch, pageType, pageProperties) {
    dispatch({
        type: 'MOVE_TO_PAGE',
        pageType,
        pageProperties
    });
}
