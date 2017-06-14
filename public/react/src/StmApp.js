import React, { Component } from 'react';
import StmSideMenu from './StmSideMenu';
import StmTopMenu from './StmTopMenu';

class StmApp extends Component {
    
    constructor(props) {
        super(props);
    }
    
    componentDidMount() {alert(this.props.serviceUrl);
        // XXX
        var oReq = new XMLHttpRequest();
        oReq.open("GET", this.props.serviceUrl, true);
        oReq.setRequestHeader("Accept", "text/json");
        
        oReq.onreadystatechange = function() {
            if (this.readyState == 4) {
               alert(this.responseText);
            }
        };
        
        oReq.send();
    }
    
    render() {
        return (
            <div className="stm-frame-outer">
                <div className="stm-frame-top">
                    <StmTopMenu />
                </div>
                <div className="stm-frame-side">
                    <StmSideMenu />
                </div>
                <div className="stm-frame-content">
                    CONTENT...
                </div>
            </div>
        );
    }
    
}

export default StmApp;
