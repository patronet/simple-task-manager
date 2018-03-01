
export default {
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
