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
    taskBoard: {

    	columnOrder: ["pending", "progress", "infinal", "accepting"],
    	columns: {
			pending: {
				title: "Várósor",
				titleColor: "#7CAC4F",
				columnColor: "#99AD86",
				tasks: [
					{
						id: 1,
						label: "Feladat 1",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 5",
						color: "#C31B1B",
					},
					{
						id: 2,
						label: "Feladat 2",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 2",
						color: "#70E28B",
					},
					{
						id: 3,
						label: "Feladat 3",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 3",
						color: "#163496",
					},
				],
			},
			progress: {
				title: "Folyamatban",
				titleColor: "#DCC728",
				columnColor: "#E6D37D",
				tasks: [
					{
						id: 4,
						label: "Feladat 4",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 1",
						color: "#10852B",
					},
				],
			},
			infinal: {
				title: "Előrehaladott",
				titleColor: "#DC7B28",
				columnColor: "#D9A477",
				tasks: [
					{
						id: 5,
						label: "Feladat 5",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 2",
						color: "#70E28B",
					},
					{
						id: 6,
						label: "Feladat 6",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 1",
						color: "#10852B",
					},
				],
			},
			accepting: {
				title: "Elfogadás alatt",
				titleColor: "#DC2837",
				columnColor: "#D18188",
				tasks: [
					{
						id: 7,
						label: "Feladat 7",
						projektLabel: "Levelezés átalakítása",
						customerLabel: "Ügyfél 4",
						color: "#BBAA99",
					},
				],
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
