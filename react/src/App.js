import React from 'react';
import { connect } from 'react-redux'
import Frame from './ConnectedComponents/AdminLayout/Frame';
import LoginPage from './ConnectedComponents/Login/LoginPage';

export default connect(state => {
    return {
        user: state.user,
    };
}, dispatch => {
    return {
    };
})(class extends React.Component {

    render() {
        if (this.props.user.isLoggedIn) {
            return <Frame />;
        } else {
            return <LoginPage />
        }
    }

});
