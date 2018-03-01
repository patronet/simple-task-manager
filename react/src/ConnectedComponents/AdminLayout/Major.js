import React from 'react';
import { connect } from 'react-redux'
import Dashboard from '../../ConnectedComponents/Dashboard/Dashboard';
import TaskBoard from '../../ConnectedComponents/TaskBoard/TaskBoard';
import Calendar from '../../ConnectedComponents/Calendar/Calendar';
import Project from '../../ConnectedComponents/Project/Project';
import ProjectList from '../../ConnectedComponents/Project/ProjectList';
import Util from '../../Util';
import { moveToPage } from '../../redux/frame/actions'



export default connect(state => {
    return state.frame.major;
}, dispatch => {
    return {
        moveToPage: (pageType, pageProperties = {}) => moveToPage(dispatch, pageType, pageProperties)
    };
})(class extends React.Component {

    render() {
        if (this.props.pageType == "taskBoard") {
            return <TaskBoard />
        } else if (this.props.pageType == "calendar") {
            return <Calendar />
        } else if (this.props.pageType == "projectList") {
            return <ProjectList onItemClicked={(projectId) => this.props.moveToPage("project", {projectId})} />;
        } else if (this.props.pageType == "project") {
            return <Project ref={Util.uniqid()} projectId={this.props.pageProperties.projectId} onSave={() => this.props.moveToPage("projectList")} />;
        } else {
            return <Dashboard />;
        }
    }

})
