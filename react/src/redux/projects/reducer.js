import update from 'react-addons-update';

export default (state = {}, action = null) => {
    if (action.type == 'REQUEST_PROJECTS') {
        return update(state, {mainProjectList: {isFetching: {$set: true}}});
    } else if (action.type == 'REQUEST_PROJECT') {
        return state;
    } else if (action.type == 'RECEIVE_PROJECTS') {
        let projectsUpdates = {};
        let projectIds = [];
        for (let i = 0; i < action.projects.length; i++) {
            let project = action.projects[i];
            let projectId = project.project_id;
            projectsUpdates[projectId] = {$set: project};
            projectIds.push(projectId);
        }
        return update(state, {
            mainProjectList: {
                wasLoaded: {$set: true},
                isFetching: {$set: false},
                projectIds: {$set: projectIds},
                pageCount: {$set: action.pageCount},
                pageNo: {$set: action.pageNo}
            },
            projects: projectsUpdates
        });
    } else if (action.type == 'RECEIVE_PROJECT') {
        return update(state, {
            projects: {
                [action.projectId]: {$set: action.project}
            }
        });
    } else if (action.type == 'POST_PROJECT') {
        return update(state, {
            projects: {
                [action.projectId]: action.updates
            }
        });
    } else if (action.type == 'POST_PROJECT_FAIL') {
        return update(state, {
            projects: {
                [action.projectId]: {$set: action.originalProject}
            }
        });
    } else {
        return state;
    }
};
