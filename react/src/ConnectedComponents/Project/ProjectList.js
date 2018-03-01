import React from 'react';
import { connect } from 'react-redux'
import { Pagination, Table } from 'semantic-ui-react'
import dataDefinitions from '../../dataDefinitions'
import { fetchProjects } from '../../redux/projects/actions'

export default connect(state => {
    return {
        projects: state.projects.projects,
        mainProjectList: state.projects.mainProjectList
    };
}, dispatch => {
    return {
        refetchProjects: (pageNo) => fetchProjects(dispatch, pageNo)
    };
})(class extends React.Component {

    componentWillMount() {
        if (!this.props.mainProjectList.wasLoaded && !this.props.mainProjectList.isFetching) {
            this.props.refetchProjects(this.props.mainProjectList.pageNo);
        }
    }

    render() {
        let projectItems = [];
        for (let i = 0; i < this.props.mainProjectList.projectIds.length; i++) {
            let projectId = this.props.mainProjectList.projectIds[i];
            let projectContainer = this.props.projects[projectId];
            let project = projectContainer.project;
            projectItems.push(
                <Table.Row key={project.project_id}>
                    <Table.Cell>{project.project_id}</Table.Cell>
                    <Table.Cell><a href="#" onClick={(e) => {e.preventDefault();this.props.onItemClicked(projectId);}}>{project.label}</a></Table.Cell>
                    <Table.Cell>{dataDefinitions.project.projectStatusInfo[project.status].label}</Table.Cell>
                    <Table.Cell>{project.has_duedate == 1 ? project.has_duedate : "- - -"}</Table.Cell>
                </Table.Row>
            );
        }

        return (
            <div>
                <div>
                    <button type="button" onClick={() => this.props.refetchProjects(this.props.mainProjectList.pageNo)}>Lista frissítése</button>
                </div>
                <div>
                    <div>{this.props.mainProjectList.isFetching ? "Loading..." : ""}</div>
                    <Table celled structured>
                        <Table.Header>
                            <Table.Row>
                                <Table.HeaderCell width={1}>ID</Table.HeaderCell>
                                <Table.HeaderCell width={7}>Projekt</Table.HeaderCell>
                                <Table.HeaderCell width={3}>Státusz</Table.HeaderCell>
                                <Table.HeaderCell width={3}>Határidő</Table.HeaderCell>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {projectItems}
                        </Table.Body>
                    </Table>

                    <div>PAGE: {this.props.mainProjectList.pageNo}</div>

                    <Pagination
                        activePage={(this.props.mainProjectList.pageNo * 1) + 1}
                        totalPages={this.props.mainProjectList.pageCount}
                        onPageChange={(event, data) => this.onPageChange(data)}
                    />
                </div>
            </div>
        );
    }

    onPageChange(data) {
        var pageNo = data.activePage - 1;
        this.props.refetchProjects(pageNo);
    }

})
