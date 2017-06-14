import React, { Component } from 'react';
import StmSideMenu from './StmSideMenu';
import StmTopMenu from './StmTopMenu';

class StmAdmin extends Component {
    
    render() {
        return (
            <div className="stm-admin-frame-outer">
                <div className="stm-admin-frame-top">
                    <StmTopMenu />
                </div>
                <div className="stm-admin-frame-side">
                    <StmSideMenu />
                </div>
                <div className="stm-admin-frame-content">
                    CONTENT...
                </div>
            </div>
        );
    }
    
}

export default StmAdmin;
