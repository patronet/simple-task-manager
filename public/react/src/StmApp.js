import React, { Component } from 'react';
import StmSideMenu from './StmSideMenu';
import StmTopMenu from './StmTopMenu';

class StmApp extends Component {
    
    constructor(props) {
        super(props);
    }
    
    componentDidMount() {
        // XXX
        var oReq = new XMLHttpRequest();
        oReq.open("GET", this.props.serviceUrl);
        oReq.responseType = "text";
        
        oReq.addEventListener("load", function (ev) {
            alert(ev.response);
        });
        
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
