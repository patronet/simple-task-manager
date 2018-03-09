import store from '../../store'
import fetchJson from '../../fetchJson';
import { showMessage } from '../frame/actions'

export function fetchProjects(dispatch, pageNo = null) {
    if (pageNo === null) {
        let state = store.getState();
        pageNo = state.projects.mainProjectList.pageNo || 0;
    }

    dispatch({type: 'REQUEST_PROJECTS'});

    var headers = null;

    return fetchJson('/api/projects?page=' + pageNo, 'GET', null, function (projects, response) {
        let pageCount = response.headers.get("x-page-count");

        var doDispatch = (projects, pageNo) => {
            dispatch({
                type: 'RECEIVE_PROJECTS',
                projects: projects,
                pageCount: pageCount,
                pageNo: pageNo,
                receivedAt: Date.now()
            });
        };

        if (pageCount > 0 && pageNo >= pageCount) {
            let manipulatedPageNo = pageCount - 1;

            fetchJson('/api/projects?page=' + manipulatedPageNo, 'GET', null, function (projects, response) {
                doDispatch(projects, manipulatedPageNo);
            });
        }

        doDispatch(projects, pageNo);
    });
}

export function fetchProject(dispatch, projectId) {
    dispatch({type: 'REQUEST_PROJECT'});

    return fetchJson('/api/projects/' + projectId, 'GET', null, function (project, response) {
        dispatch({
            type: 'RECEIVE_PROJECT',
            projectId: project.project_id,
            project: project,
            receivedAt: Date.now()
        });
    });
}

export function postUpdateProject(dispatch, updates, changesToPost, projectId, callback) {
    let originalState = store.getState();
    let originalProject = originalState.projects.projects[projectId];

    dispatch({
        type: 'POST_PROJECT',
        projectId: projectId,
        updates: updates,
        changesToPost: changesToPost,
    });
    if (callback) {
        callback();
    }

    return fetchJson('/api/projects/' + projectId, 'POST', changesToPost, function (result, response) {
        if (!result.success) {
            showMessage(dispatch, result.message, "Hiba a mentéskor", "error");
            dispatch({
                type: 'POST_PROJECT_FAIL',
                message: result.message,
                projectId: projectId,
                originalProject: originalProject,
            });
            return;
        }
    });
}

export function postCreateProject(dispatch, changesToPost, callback) {
    return fetchJson('/api/projects', 'POST', changesToPost, function (result, response) {
        if (!result.success) {
            showMessage(dispatch, result.message, "Hiba a mentéskor", "error");
            return;
        }

        // XXX
        callback();
    });
}

export function deleteProject(dispatch, projectId, callback) {
    return fetchJson('/api/projects/' + projectId + '?_method=delete', 'GET', null, function (result, response) {
        if (!result.success) {
            showMessage(dispatch, result.message, "Hiba a mentéskor", "error");
            return;
        }

        // XXX
        callback();
    });
}
