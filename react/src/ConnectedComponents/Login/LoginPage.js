import React from 'react';
import { connect } from 'react-redux'
import { Button, Form, Input } from 'semantic-ui-react'
import fetchJson from '../../fetchJson'
import { tryLogin } from '../../redux/user/actions'

export default connect(state => {
    return {
        user: state.user
    };
}, dispatch => {
    return {
        tryLogin: (username, password) => tryLogin(dispatch, username, password),
    };
})(class extends React.Component {

    constructor() {
        super();
        this.state = {
            username: "",
            password: "",
        };
    }

    componentWillMount() {
        this.setState({
            password: "",
        });
    }

    render() {
        return (
            <Form style={{
                margin: "30px auto",
                width: "400px",
            }} as="div">
                <h2>Bejelentkezés</h2>
                <Form.Field>
                    <label>Felhasználónév:</label>
                    <Input ref="username" value={this.state.username} onChange={(ev, data) => this.setState({username: data.value})} />
                </Form.Field>
                <Form.Field>
                    <label>Jelszó:</label>
                    <Input type="password" ref="password" value={this.state.password} onChange={(ev, data) => this.setState({password: data.value})} />
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
        this.props.tryLogin(this.state.username, this.state.password);
    }

})
