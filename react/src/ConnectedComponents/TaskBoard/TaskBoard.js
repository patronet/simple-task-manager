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
                <h2>Task Board</h2>
                <p>
                    Pending - Progress - Late state - Approval
                </p>
            </div>
        );
    }

})
