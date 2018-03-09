import fetchJson from '../../fetchJson';

export function refreshDashboard(dispatch) {
    dispatch({
        type: 'REQUEST_DASHBOARD',
    });

    return fetchJson('/api/dashboard', 'GET', null, function (resultData, response) {
        dispatch({
            type: 'RECEIVE_DASHBOARD',
            activeProjectCount: resultData.activeProjectCount,
            receivedAt: Date.now()
        });
    });
}
