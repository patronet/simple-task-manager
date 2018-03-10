export default {
    user: {
        isLoggedIn: true,
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
            pageType: "taskBoard",
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
    taskBoard: {
    	
    	columnOrder: ["columnA", "columnB"],
    	columns: {
			columnA: {
				// XXX
				items: Array.from({ length: 10 }, (v, k) => k).map(k => ({
					id: `item-a-${k}`,
					content: `item A${k}`,
				})),
			},
			columnB: {
				// XXX
				items: Array.from({ length: 10 }, (v, k) => k).map(k => ({
					id: `item-b-${k}`,
					content: `item B${k}`,
				})),
			},
    	},
    	
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
