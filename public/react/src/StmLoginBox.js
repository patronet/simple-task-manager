import React, { Component } from 'react';

class StmLoginBox extends Component {
    
    logIn() {
        this.props.app.setState({"loggedIn":true});
    }

    render() {
        return (
            <div class="stm-login-box">
                <input type="button" value="login" onClick={ () => this.logIn() } />
            </div>
        );
    }
    
}

export default StmLoginBox;
