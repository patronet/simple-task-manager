export default {
    user: {
        isLoggedIn: false,
        logInData: null,
        userData: {


            "user_id": "1",
            "username": "horvath@patronet.net",
            "email": "horvath@patronet.net",
            "name": "Horváth Dávid",
            "image": null

        },
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
            pageType: "dashboard",
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
    tasks: {
        taskBoard: {

        	columnOrder: ["pending", "progress", "infinal", "accepting"],
        	columns: {
    			pending: {
    				title: "Várósor",
    				titleColor: "#7CAC4F",
    				columnColor: "#99AD86",
    				tasks: [1, 2, 3],
    			},
    			progress: {
    				title: "Folyamatban",
    				titleColor: "#DCC728",
    				columnColor: "#E6D37D",
    				tasks: [4],
    			},
    			infinal: {
    				title: "Előrehaladott",
    				titleColor: "#DC7B28",
    				columnColor: "#D9A477",
    				tasks: [5, 6],
    			},
    			accepting: {
    				title: "Elfogadás alatt",
    				titleColor: "#DC2837",
    				columnColor: "#D18188",
    				tasks: [7],
    			},
        	},

        },

        tasks: {
            1: {
                id: 1,
                label: "Feladat 1",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 5",
                color: "#C31B1B",
            },
            2: {
                id: 2,
                label: "Feladat 2",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 2",
                color: "#70E28B",
            },
            3: {
                id: 3,
                label: "Feladat 3",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 3",
                color: "#163496",
            },
            4: {
                id: 4,
                label: "Feladat 4",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 1",
                color: "#10852B",
            },
            5: {
                id: 5,
                label: "Feladat 5",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 2",
                color: "#70E28B",
            },
            6: {
                id: 6,
                label: "Feladat 6",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 1",
                color: "#10852B",
            },
            7: {
                id: 7,
                label: "Feladat 7",
                projektLabel: "Levelezés átalakítása",
                customerLabel: "Ügyfél 4",
                color: "#BBAA99",
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
