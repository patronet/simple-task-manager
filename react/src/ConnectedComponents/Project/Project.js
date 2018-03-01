import React from 'react';
import update from 'react-addons-update';
import { connect } from 'react-redux'
import { Checkbox, Dropdown, Input, Form, Table } from 'semantic-ui-react'
import Util from '../../Util'
import dataDefinitions from '../../dataDefinitions'
import { fetchProject, postProject } from '../../redux/projects/actions'

export default connect(state => {
    return {
        projects: state.projects.projects
    };
}, dispatch => {
    return {
        refetchProject: (projectId) => fetchProject(dispatch, projectId),
        postProject: (updates, changesToPost, projectId, callback) => postProject(dispatch, updates, changesToPost, projectId, callback)
    };
})(class extends React.Component {

    constructor() {
        super();
        this.state = {
            changesToPost: {},
            updates: {}
        };
    }

    componentWillMount() {
        if (!(this.props.projectId in this.props.projects)) {
            this.props.refetchProject(this.props.projectId);
        }
    }

    render() {
        if (!(this.props.projectId in this.props.projects)) {
            return (<div>Betöltés</div>);
        }

        if (!this.props.projects[this.props.projectId]) {
            return (<div>Hiba a betöltéskor!</div>);
        }

        let editedProjectContainer = update(this.props.projects[this.props.projectId], this.state.updates);
        let editedProject = editedProjectContainer.project;

        let statusOptions = [];
        for (let status of dataDefinitions.project.projectStatuses) {
            let statusInfo = dataDefinitions.project.projectStatusInfo[status];
            statusOptions.push({
                value: status,
                text: statusInfo.label,
                icon: statusInfo.icon,
            });
        }

        let sprintItems = [];
        for (let sprintContainer of editedProjectContainer.sprints) {
            let sprint = sprintContainer.sprint;
            sprintItems.push(
                <Table.Row key={sprint.sprint_id}>
                    <Table.Cell>{sprint.sprint_id}</Table.Cell>
                    <Table.Cell><a href="#" onClick={(e) => {e.preventDefault();alert(sprint.sprint_id);}}>{sprint.label}</a></Table.Cell>
                    <Table.Cell>---</Table.Cell> {/*TODO*/}
                    <Table.Cell>---</Table.Cell> {/*TODO*/}
                </Table.Row>
            );
        }

        return (
            <Form>
                <div style={{position:"fixed",right:"20px",border:"2px solid #999999"}}>
                    <p>
                        <button onClick={() => this.save()}>OK</button>
                    </p>
                </div>
                <h2>Projekt adatai</h2>
                <p>
                    Projekt megnevezése:
                    <Input
                        value={editedProject.label}
                        onChange={(ev) => this.injectSimpleChange("label", ev.target.value)}
                    />
                </p>
                <p>
                    Státusz:
                    <Dropdown
                        selection placeholder="Válasszon"
                        value={editedProject.status}
                        onChange={(ev, data) => this.injectSimpleChange("status", data.value)}
                        options={statusOptions}
                    />
                </p>
                <p>
                    Kezdődátum:
                    <Checkbox
                        label="Dátumozott"
                        checked={editedProject.has_startdate == "1"}
                        onChange={(ev, data) => this.injectSimpleChange("has_startdate", data.checked ? "1" : "0")}
                    />
                    <Input
                        placeholder="Kezdődátum"
                        value={editedProject.date_startdate || ""}
                        onChange={(ev) => this.injectSimpleChange("date_startdate", ev.target.value, true)}
                    /> {/* TODO: datepicker */}
                </p>
                <p>
                    Záródátum:
                    <Checkbox
                        label="Dátumozott"
                        checked={editedProject.has_duedate == "1"}
                        onChange={(ev, data) => this.injectSimpleChange("has_duedate", data.checked ? "1" : "0")}
                    />
                    <Input
                        placeholder="Záródátum"
                        value={editedProject.date_duedate || ""}
                        onChange={(ev) => this.injectSimpleChange("date_duedate", ev.target.value, true)}
                    /> {/* TODO: datepicker */}
                </p>
                <Form.Field>
                    Leírás:
                    <textarea
                        value={editedProject.description}
                        onChange={(ev) => this.injectSimpleChange("description", ev.target.value)}
                    />
                </Form.Field>
                <hr />
                <div>
                    <div>
                        Projekt sprintjei
                        <button onClick={() => {}}>+</button>
                    </div>
                    {
                        sprintItems.length > 0 ? (
                            <Table celled structured>
                                <Table.Header>
                                    <Table.Row>
                                        <Table.HeaderCell width={1}>ID</Table.HeaderCell>
                                        <Table.HeaderCell width={5}>Név</Table.HeaderCell>
                                        <Table.HeaderCell width={3}>Kezdés</Table.HeaderCell>
                                        <Table.HeaderCell width={3}>Határidő</Table.HeaderCell>
                                    </Table.Row>
                                </Table.Header>
                                <Table.Body>
                                    {sprintItems}
                                </Table.Body>
                            </Table>
                        ) : (
                            <div>Nincs hozzáadva sprint</div>
                        )
                    }
                </div>
            </Form>
        );
    }

    injectSimpleChange(key, value, nullIfEmpty = false) {
        let newValue = (nullIfEmpty && !value) ? null : value;

        // XXX
        let newUpdates = this.state.updates;
        newUpdates.project = newUpdates.project || {};
        newUpdates.project[key] = {$set: newValue};

        // XXX
        let newchangesToPost = this.state.changesToPost;
        newchangesToPost.project = newchangesToPost.project || {};
        newchangesToPost.project[key] = newValue;

        this.setState({updates: newUpdates, changesToPost: newchangesToPost});
    }

    save() {
        if (Util.isEmptyObject(this.state.changesToPost)) {
            this.props.onSave();
        } else {
            this.props.postProject(this.state.updates, this.state.changesToPost, this.props.projectId, this.props.onSave);
        }
    }

})
