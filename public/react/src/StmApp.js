import React, { Component } from 'react';
import StmSideMenu from './StmSideMenu';
import StmTopMenu from './StmTopMenu';

class StmApp extends Component {
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
