import React from 'react';
import { connect } from 'react-redux'
import { fetchSprints } from '../redux/sprints/actions'
import { Pagination, Table } from 'semantic-ui-react'

export default connect(state => {
    return {
        sprints: state.sprints.sprints,
        mainSprintList: state.sprints.mainSprintList
    };
}, dispatch => {
    return {
        refetchSprints: (pageNo) => fetchSprints(pageNo, dispatch)
    };
})(class extends React.Component {

    render() {
        let sprintItems = [];
        for (let i = 0; i < this.props.mainSprintList.sprintIds.length; i++) {
            let sprintId = this.props.mainSprintList.sprintIds[i];
            let sprintContainer = this.props.sprints[sprintId];
            let sprint = sprintContainer.sprint;
            sprintItems.push(
                <Table.Row key={sprint.sprint_id}>
                    <Table.Cell>{sprint.sprint_id}</Table.Cell>
                    <Table.Cell><a href="#" onClick={(e) => {e.preventDefault();}}>{sprint.label}</a></Table.Cell>
                    <Table.Cell>{sprint.status}</Table.Cell>
                </Table.Row>
            );
        }

        return (
            <div>
                <div>
                    <button type="button" onClick={() => this.props.refetchSprints(this.props.mainSprintList.pageNo)}>Fetch list</button>
                </div>
                <div>
                    <div>{this.props.mainSprintList.isFetching ? "Loading..." : ""}</div>
                    <Table celled structured>
                        <Table.Header>
                            <Table.Row>
                                <Table.HeaderCell width={1}>ID</Table.HeaderCell>
                                <Table.HeaderCell width={7}>Projekt</Table.HeaderCell>
                                <Table.HeaderCell width={3}>Státusz</Table.HeaderCell>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {sprintItems}
                        </Table.Body>
                    </Table>

                    <div>PAGE: {this.props.mainSprintList.pageNo}</div>

                    <Pagination
                        activePage={(this.props.mainSprintList.pageNo * 1) + 1}
                        totalPages={this.props.mainSprintList.pageCount}
                        onPageChange={(event, data) => this.onPageChange(data)}
                    />
                </div>
            </div>
        );
    }

    onPageChange(data) {
        var pageNo = data.activePage - 1;
        this.props.refetchSprints(pageNo);
    }

})
