
function requestSprints() {
    return {
        type: 'REQUEST_SPRINTS'
    }
}

function receiveSprints(data) {
    return {
        type: 'RECEIVE_SPRINTS',
        sprints: data.sprints,
        pageCount: data.pageCount,
        pageNo: data.pageNo,
        receivedAt: Date.now()
    }
}

export function fetchSprints(pageNo, dispatch) {
    dispatch(requestSprints());

    var result = {};

    return (
        fetch('/api/sprints?page=' + pageNo, {
            method: 'GET',
            credentials: "same-origin",
            headers: new Headers({
                'Accept': 'text/json'
            })
        }).then(function(response) {
            result.pageCount = response.headers.get("x-page-count");
            return response.json();
        }).then(function(sprints) {
            result.sprints = sprints;
            result.pageCount = result.pageCount;
            result.pageNo = pageNo;
            dispatch(receiveSprints(result));
        })
    );
}
