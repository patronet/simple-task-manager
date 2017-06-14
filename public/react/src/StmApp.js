import React, { Component } from 'react';
import StmLogin from './StmLogin';
import StmAdmin from './StmAdmin';

class StmApp extends Component {
    
    constructor(props) {
        super(props);
        this.state = {
            loggedIn: false,
        }
    }
    
    render() {
        var screen = this.state.loggedIn ? <StmAdmin />: <StmLogin app={this} />
        return (
            <div className="stm-frame-outer">
                {screen}
            </div>
        );
    }
    
}

export default StmApp;
