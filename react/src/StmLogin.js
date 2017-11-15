import React, { Component } from 'react';
import StmLoginBox from './StmLoginBox';

class StmLogin extends Component {
    
    render() {
        return (
            <div class="stm-login-outer">
                <StmLoginBox app={this.props.app} />
            </div>
        );
    }
    
}

export default StmLogin;
