import store from '../../store'
import { showMessage } from '../frame/actions'

export function fetchProjects(dispatch, pageNo = null) {
    if (pageNo === null) {
        let state = store.getState();
        pageNo = state.projects.mainProjectList.pageNo || 0;
    }

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
            let pageCount = headers.get("x-page-count");

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

                fetch('/api/projects?page=' + manipulatedPageNo, {
                    method: 'GET',
                    credentials: "same-origin",
                    headers: new Headers({
                        'Accept': 'text/json'
                    })
                }).then(function(response) {
                    return response.json();
                }).then(function(projects) {
                    doDispatch(projects, manipulatedPageNo);
                });
            }

            doDispatch(projects, pageNo);
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

export function postUpdateProject(dispatch, updates, changesToPost, projectId, callback) {
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

export function postCreateProject(dispatch, changesToPost, callback) {

    // XXX
    return (
        fetch('/api/projects', {
            method: 'POST',
            credentials: "same-origin",
            headers: new Headers({
                'Content-type': 'text/json',
                'Accept': 'text/json'
            }),
            body: JSON.stringify(changesToPost.project) // FIXME: full container? changes?
        }).then(function(response) {
            return response.json();
        }).then(function(result) {
            if (!result.success) {
                showMessage(dispatch, result.message, "Hiba a mentéskor", "error");
                return;
            }

            // XXX
            callback();
        })
    );
}

export function deleteProject(dispatch, projectId, callback) {
    return (
        fetch('/api/projects/' + projectId + '?_method=delete', {
            method: 'GET',
            credentials: "same-origin",
            headers: new Headers({
                'Content-type': 'text/json',
                'Accept': 'text/json'
            }),
        }).then(function(response) {
            return response.json();
        }).then(function(result) {
            if (!result.success) {
                showMessage(dispatch, result.message, "Hiba a mentéskor", "error");
                return;
            }

            // XXX
            callback();
        })
    );
}
