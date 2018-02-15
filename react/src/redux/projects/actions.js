
function requestProjects() {
    return {
        type: 'REQUEST_PROJECTS'
    }
}

function receiveProjects(data) {
    return {
        type: 'RECEIVE_PROJECTS',
        projects: data.projects,
        pageCount: data.pageCount,
        pageNo: data.pageNo,
        receivedAt: Date.now()
    }
}

export function fetchProjects(pageNo, dispatch) {
    dispatch(requestProjects());

    var result = {};

    return (
        fetch('/api/projects?page=' + pageNo, {
            method: 'GET',
            credentials: "same-origin",
            headers: new Headers({
                'Accept': 'text/json'
            })
        }).then(function(response) {
            result.pageCount = response.headers.get("x-page-count");
            return response.json();
        }).then(function(projects) {
            result.projects = projects;
            result.pageCount = result.pageCount;
            result.pageNo = pageNo;
            dispatch(receiveProjects(result));
        })
    );
}
