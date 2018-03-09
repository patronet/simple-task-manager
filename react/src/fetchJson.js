import base64 from 'base-64';
import store from './store'
import { showMessage } from './redux/frame/actions'
import { clearLogin } from './redux/user/actions'

export default function (
    url, method, data,
    callback = ((resultData, response) => {}),
    errorCallback = ((response, information) => {})
) {
    let state = store.getState();

    let headers = {
        'Accept': 'text/json'
    };
    if (data !== null) {
        headers['Content-type'] = 'text/json';
    }
    if (state.user && state.user.logInData && state.user.logInData.username) {
        let loginString = state.user.logInData.username;
        if (state.user.logInData.password) {
            loginString += ":" + state.user.logInData.password;
        }
        headers['Authorization'] = 'Basic ' + base64.encode(loginString);
    }

    let fetchParameters = {
        method,
        credentials: "same-origin",
        headers: new Headers(headers)
    };
    if (data !== null) {
        fetchParameters.body = JSON.stringify(data);
    }

    let response = null;
    return fetch(url, fetchParameters).then(function(_response) {
        response = _response;
        return _response.json();
    }).then(function(resultData) {
        if (response.status == 401) {
            if (state.user.isLoggedIn) {
                showMessage(store.dispatch, "A szerver üzenete: " + resultData.message, "Megszűnt bejelentkezés", "error", () => {
                    clearLogin(store.dispatch);
                }, "Tovább a bejelentkezéshez", false);
            }
            errorCallback(response, {
                message: resultData.message || "Unknown error",
                resultData: resultData,
            });
            return;
        }
        callback(resultData, response);
    }).catch(function (requestError) {
        errorCallback(response, {
            message: requestError.message,
            requestError: requestError
        });
    });
}
