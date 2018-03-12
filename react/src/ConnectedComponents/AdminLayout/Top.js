import React from 'react';
import { connect } from 'react-redux'
import { Button, Icon, Image, Input, Menu } from 'semantic-ui-react';
import { logOut } from '../../redux/user/actions'
import { showMessage } from '../../redux/frame/actions'

export default connect(state => {
    return {
        frame: state.frame,
        user: state.user,
    };
}, dispatch => {
    return {
        logOut: () => logOut(dispatch),
        showMessage: (message, title, messageType, action) => showMessage(dispatch, message, title, messageType, action),
    };
})(class extends React.Component {

    render() {console.log(this.props.user);
        var activeItem = "home";
        return (
            <div style={{overflow:"hidden"}}>
                <div style={{float:"left"}}>
                    <Button inverted basic>
                        {
                            this.props.user.userData.image ?
                            <Image src={this.props.user.userData.image} avatar /> :
                            <Icon name="user" />
                        }
                        {this.props.user.userData.name}
                    </Button>
                </div>
                <div style={{float:"right"}}>
                    <Button onClick={() => this.logOut()} color="red">
                        <Icon
                            name="shutdown"
                            size="large"
                        />
                        Kilépés
                    </Button>
                </div>
            </div>
        )
    }

    logOut() {
        this.props.showMessage("Biztosan kilép a rendszerből?", "Kilépés", "warning", () => {
            this.props.logOut();
        }, "Kilépés");
    }

});
