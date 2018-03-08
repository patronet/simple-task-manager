import React from 'react';
import { connect } from 'react-redux'
import { Button, Form, Input } from 'semantic-ui-react'

export default connect(state => {
    return {
        user: state.user
    };
}, dispatch => {
    return {
        openFakeLogin: () => dispatch({
            type: 'LOGGED_IN',
            // TODO
        })
    };
})(class extends React.Component {

    render() {
        return (
            <Form style={{
                margin: "30px auto",
                width: "400px",
            }}>
                <h2>Bejelentkezés</h2>
                <Form.Field>
                    <label>Felhasználónév:</label>
                    <Input ref="username" />
                </Form.Field>
                <Form.Field>
                    <label>Jelszó:</label>
                    <Input type="password" ref="password" />
                </Form.Field>
                <p>
                    <Button onClick={() => this.submitLogin()}>
                        Bejelentkezés
                    </Button>
                </p>
            </Form>
        );
    }

    submitLogin() {
        this.props.openFakeLogin();
    }

})
