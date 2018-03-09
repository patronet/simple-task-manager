export default {
    user: {
        isLoggedIn: false,
        logInData: null,
        userData: null,
    },
    frame: {
        modal: {
            isOpen: false,
            modalType: "info",
            title: "A message",
            content: "Hello!",
        },
        side: {
            visible: true,
            minimized: false,
        },
        major: {
            pageType: "projectList",
            pageProperties: {
                //projectId: 1,
            },
        },
    },
    dashboard: {
        isPopulated: true,
        isFetching: false,
        activeProjectCount: 77,
    },
    projects: {
        mainProjectList: {
            wasLoaded: false,
            isFetching: false,
            pageNo: 0,
            pageCount: 0,
            projectIds: [],
        },
        projects: {},
    },
};
