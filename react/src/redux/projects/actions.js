import store from '../../store'
import { showMessage } from '../frame/actions'

export function fetchProjects(dispatch, pageNo) {
    dispatch({type: 'REQUEST_PROJECTS'});

    var headers = null;

    return (
        fetch('/api/projects?page=' + pageNo, {
            method: 'GET',
            credentials: "same-origin",
            headers: new Headers({
                'Accept': 'text/json'
            })
        }).then(function(response) {
            headers = response.headers;
            return response.json();
        }).then(function(projects) {
            dispatch({
                type: 'RECEIVE_PROJECTS',
                projects: projects,
                pageCount: headers.get("x-page-count"),
                pageNo: pageNo,
                receivedAt: Date.now()
            });
        })
    );
}

export function fetchProject(dispatch, projectId) {
    dispatch({type: 'REQUEST_PROJECT'});

    return (
        fetch('/api/projects/' + projectId, {
            method: 'GET',
            credentials: "same-origin",
            headers: new Headers({
                'Accept': 'text/json'
            })
        }).then(function(response) {
            return response.json();
        }).then(function(projectContainer) {
            dispatch({
                type: 'RECEIVE_PROJECT',
                projectId: projectContainer.project.project_id,
                projectContainer: projectContainer,
                receivedAt: Date.now()
            });
        })
    );
}

export function postProject(dispatch, updates, changesToPost, projectId, callback) {
    let originalState = store.getState();
    let originalProjectContainer = originalState.projects.projects[projectId];

    dispatch({
        type: 'POST_PROJECT',
        projectId: projectId,
        updates: updates,
        changesToPost: changesToPost,
    });
    if (callback) {
        callback();
    }

    return (
        fetch('/api/projects/' + projectId, {
            method: 'POST',
            credentials: "same-origin",
            headers: new Headers({
                'Content-type': 'text/json',
                'Accept': 'text/json'
            }),
            body: JSON.stringify(changesToPost.project) // FIXME: full container?
        }).then(function(response) {
            return response.json();
        }).then(function(result) {
            if (!result.success) {
                showMessage(dispatch, result.message, "Hiba a mentéskor", "error");
                dispatch({
                    type: 'POST_PROJECT_FAIL',
                    message: result.message,
                    projectId: projectId,
                    originalProjectContainer: originalProjectContainer,
                });
                return;
            }
        })
    );
}
