import update from 'react-addons-update';

export default (state = [], action) => {
    if (action.type == 'REQUEST_PROJECTS') {
        return update(state, {mainProjectList: {isFetching: {$set: true}}});
    } else if (action.type == 'RECEIVE_PROJECTS') {
        let projectsUpdates = {};
        let projectIds = [];
        for (let i = 0; i < action.projects.length; i++) {
            let projectContainer = action.projects[i];
            let project = projectContainer.project;
            let projectId = project.project_id;
            projectsUpdates[projectId] = {$set: projectContainer};
            projectIds.push(projectId);
        }
        return update(state, {
            mainProjectList: {
                isFetching: {$set: false},
                projectIds: {$set: projectIds},
                pageCount: {$set: action.pageCount},
                pageNo: {$set: action.pageNo}
            },
            projects: projectsUpdates
        });
    } else {
        return state;
    }
};
