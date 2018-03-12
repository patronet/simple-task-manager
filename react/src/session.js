import testData from './testData'

export let getInitialState = function () {
    return mergeSerializedSession(testData);
}

// TODO: do not load too old state
// TODO: serialize only the session datas

export let serializeSession = function (state) {
    if (window.sessionStorage) {
        let stateInfo = {
            // ...
            state: state,
        };
        window.sessionStorage.setItem('serializedState', JSON.stringify(stateInfo));
        console.log("serialized");
    }
};

export let mergeSerializedSession = function (state) {
    if (window.sessionStorage) {
        let serializedState = window.sessionStorage.getItem('serializedState');
        if (serializedState !== null) {
            let stateInfo = JSON.parse(serializedState);
            return stateInfo.state;
        }
    }
    return state;
}
