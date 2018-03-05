import React from 'react';
import { connect } from 'react-redux'
import Dashboard from '../../ConnectedComponents/Dashboard/Dashboard';
import TaskBoard from '../../ConnectedComponents/TaskBoard/TaskBoard';
import Calendar from '../../ConnectedComponents/Calendar/Calendar';
import Project from '../../ConnectedComponents/Project/Project';
import ProjectList from '../../ConnectedComponents/Project/ProjectList';
import Util from '../../Util';
import { moveToPage } from '../../redux/frame/actions'
import { fetchProjects } from '../../redux/projects/actions'



export default connect(state => {
    return state.frame.major;
}, dispatch => {
    return {
        moveToPage: (pageType, pageProperties = {}) => moveToPage(dispatch, pageType, pageProperties),
        fetchProjects: () => fetchProjects(dispatch),
    };
})(class extends React.Component {

    render() {
        let currentProps = this.props;

        if (currentProps.pageType == "taskBoard") {
            return <TaskBoard />
        } else if (currentProps.pageType == "calendar") {
            return <Calendar />
        } else if (currentProps.pageType == "projectList") {
            return <ProjectList
                onItemClicked={(projectId) => currentProps.moveToPage("project", {projectId})}
                onAdd={() => currentProps.moveToPage("project", {projectId: null})}
            />;
        } else if (currentProps.pageType == "project") {
            return <Project
                ref={Util.uniqid()}
                projectId={currentProps.pageProperties.projectId}
                onSave={() => {
                    currentProps.moveToPage("projectList");
                    if (!currentProps.pageProperties.projectId) {
                        this.props.fetchProjects();
                    }
                }}
                onDelete={() => {
                    currentProps.moveToPage("projectList");
                    this.props.fetchProjects();
                }}
            />;
        } else {
            return <Dashboard />;
        }
    }

})
