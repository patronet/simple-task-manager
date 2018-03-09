import React from 'react';
import { connect } from 'react-redux'
import Frame from './ConnectedComponents/AdminLayout/Frame';
import LoginPage from './ConnectedComponents/Login/LoginPage';
import MainModal from './MainModal';

import 'semantic-ui-css/semantic.min.css';

export default connect(state => {
    return {
        user: state.user,
    };
}, dispatch => {
    return {
    };
})(class extends React.Component {

    render() {
        return (
            <div>
                {this.props.user.isLoggedIn ? <Frame /> : <LoginPage />}
                <MainModal />
            </div>
        );
    }

});
