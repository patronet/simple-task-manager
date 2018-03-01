import React from 'react';
import Top from './Top';
import Side from './Side';
import Major from './Major';
import { Menu, Icon, Segment } from 'semantic-ui-react'
import { connect } from 'react-redux'
import { fetchProject } from '../../redux/projects/actions'

import 'semantic-ui-css/semantic.min.css';
import './frame.css';

export default connect(state => {
    return {
        frame: state.frame
    };
}, dispatch => {
    return {
        //refetchProject: (projectId) => fetchProject(dispatch, projectId)
    };
})(class extends React.Component {

    render() {
        return (
            <div className="frame-outer">
                <div className="frame-top">
                    <Top />
                </div>
                <div className="frame-page">
                    <div className="frame-side">
                        <Side minimized={this.props.frame.side.minimized} />
                    </div>
                    <div className="frame-major">
                        <Major />
                    </div>
                </div>
            </div>
        );
    }

})
