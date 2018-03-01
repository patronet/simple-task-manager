import React from 'react';
import { connect } from 'react-redux'

export default connect(state => {
    return {
        // ...
    };
}, dispatch => {
    return {
        // ...
    };
})(class extends React.Component {

    componentWillMount() {
        // TODO: load dashboard data
    }

    render() {
        let activeProjectCount = 2;
        return (
            <div>
                <h2>Naptár</h2>
                <p>
                    {new Date().toDateString()}
                </p>
            </div>
        );
    }

})
