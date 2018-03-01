
export function refreshDashboard(dispatch) {
    dispatch({
        type: 'REQUEST_DASHBOARD',
    });

    return (
        fetch('/api/dashboard', {
            method: 'GET',
            credentials: "same-origin",
            headers: new Headers({
                'Accept': 'text/json'
            })
        }).then(function(response) {
            return response.json();
        }).then(function(dashboardData) {
            dispatch({
                type: 'RECEIVE_DASHBOARD',
                activeProjectCount: dashboardData.activeProjectCount,
                receivedAt: Date.now()
            });
        })
    );
}
