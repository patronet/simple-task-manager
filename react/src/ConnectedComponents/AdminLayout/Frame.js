import { connect } from 'react-redux'
import React from 'react';
import Top from './Top';
import Side from './Side';
import Major from './Major';
import { Menu } from 'semantic-ui-react'

import './frame.css';

export default connect(state => {
    return state.frame;
}, dispatch => {
    return {
    };
})(class extends React.Component {

    render() {
        return (
            <div>
                <div className="frame-outer">
                    <div className="frame-top">
                        <Top />
                    </div>
                    <div className="frame-page">
                        <div className="frame-side">
                            <Side minimized={this.props.side.minimized} />
                        </div>
                        <div className="frame-major">
                            <Major />
                        </div>
                    </div>
                </div>
            </div>
        );
    }

})
