
export default {
    frame: {
        modal: {
            modalTypeInfo: {
                "info": {
                    icon: "info circle",
                    color: "blue",
                },
                "help": {
                    icon: "help circle",
                    color: "blue",
                },
                "warning": {
                    icon: "warning sign",
                    color: "orange",
                },
                "error": {
                    icon: "warning circle",
                    color: "red",
                },
            }
        },
    },
    project: {
        projectStatuses: ["initial", "progress", "canceled", "completed"],
        projectStatusInfo: {
            initial: {label: "Létrehozva", icon: "asterisk"},
            progress: {label: "Folyamatban", icon: "hourglass empty"},
            canceled: {label: "Visszavonva", icon: "remove"},
            completed: {label: "Befejezezve", icon: "check"},
        },
    },
}
