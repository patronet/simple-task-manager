import React from 'react';
import { connect } from 'react-redux'
import { Icon } from 'semantic-ui-react'
import { refreshDashboard } from '../../redux/dashboard/actions'

export default connect(state => {
    return state.dashboard;
}, dispatch => {
    return {
        refreshDashboard: () => refreshDashboard(dispatch)
    };
})(class extends React.Component {

    componentWillMount() {
        // TODO: load dashboard data
    }

    render() {
        if (!this.props.isPopulated) {
            return (<div>
                {this.props.isFetching ? "Betöltés..." : "Az adatok jelenleg nem elérhetőek"}
            </div>);
        }

        return (
            <div>
                <h2>Dashboard {this.props.isFetching ? <Icon name="refresh" loading /> : null}</h2>
                <p>
                    <button onClick={() => this.props.refreshDashboard()}>Újratöltés (a szerverről)</button>
                </p>
                <p>
                    Jelenleg {this.props.activeProjectCount} aktív projekt van a rendszerben
                </p>
            </div>
        );
    }

})
