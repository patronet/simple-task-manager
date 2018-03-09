import fetchJson from '../../fetchJson';
import { showMessage } from '../frame/actions';

export function tryLogin(dispatch, username, password) {
    dispatch({
        type: 'SET_LOGIN',
        username, password
    });

    return fetchJson("/api", "GET", null, (resultData) => {
        dispatch({type: 'ACCEPT_LOGIN'});
    }, (response, information) => {
        showMessage(dispatch, information.message, "Sikertelen bejelentkezés", "error");
    });
}

export function acceptLogin(dispatch) {
    dispatch({type: 'ACCEPT_LOGIN'});
}

export function clearLogin (dispatch) {
    dispatch({type: 'CLEAR_LOGIN'});
}

export function logOut(dispatch) {
    dispatch({type: 'LOG_OUT'});
}
