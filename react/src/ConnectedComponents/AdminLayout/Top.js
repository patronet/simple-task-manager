import React from 'react';
import { connect } from 'react-redux'
import { Button, Icon, Image, Input, Menu } from 'semantic-ui-react';
import { logOut } from '../../redux/user/actions'
import { showMessage } from '../../redux/frame/actions'

export default connect(state => {
    return state.frame;
}, dispatch => {
    return {
        logOut: () => logOut(dispatch),
        showMessage: (message, title, messageType, action) => showMessage(dispatch, message, title, messageType, action),
    };
})(class extends React.Component {

    render() {
        var activeItem = "home";
        return (
            <div style={{overflow:"hidden"}}>
                <div style={{float:"left"}}>
                    <Image src="https://thefinanser.com/wp-content/uploads/2015/12/6a01053620481c970b01b7c7617a9f970b-600wi.jpg" avatar />
                    Teszt Jakab
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
