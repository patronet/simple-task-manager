import React from 'react';
import update from 'react-addons-update';
import { connect } from 'react-redux'
import { Button, Checkbox, Dropdown, Input, Form, Table } from 'semantic-ui-react'
import Util from '../../Util'
import dataDefinitions from '../../dataDefinitions'
import { showMessage } from '../../redux/frame/actions'
import { deleteProject, fetchProject, postUpdateProject, postCreateProject } from '../../redux/projects/actions'

export default connect(state => {
    return {
        projects: state.projects.projects
    };
}, dispatch => {
    return {
        showMessage: (message, title, messageType, action) => showMessage(dispatch, message, title, messageType, action),
        fetchProject: (projectId) => fetchProject(dispatch, projectId),
        postUpdateProject: (updates, changesToPost, projectId, callback) => postUpdateProject(dispatch, updates, changesToPost, projectId, callback),
        postCreateProject: (changesToPost, callback) => postCreateProject(dispatch, changesToPost, callback),
        deleteProject: (projectId, callback) => deleteProject(dispatch, projectId, callback),
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
            this.props.fetchProject(this.props.projectId);
        }
    }

    render() {
        let editedProjectContainer =  {project: {}};
        let editedProject = {};

        if (this.props.projectId !== null) {
            if (!(this.props.projectId in this.props.projects)) {
                return (<div>Betöltés</div>);
            }

            if (!this.props.projects[this.props.projectId]) {
                return (<div>Hiba a betöltéskor!</div>);
            }

            editedProjectContainer = update(this.props.projects[this.props.projectId], this.state.updates);
            editedProject = editedProjectContainer.project;
        }

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
        if (editedProjectContainer && editedProjectContainer.sprints) {
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
        }

        // XXX: default values

        return (
            <Form>
                <div style={{position:"fixed",right:"20px"}}>
                    <p>
                        <Button color="green" onClick={() => this.save()}>Rendben</Button>
                    </p>
                </div>
                <h2>{this.props.projectId ? "Projekt adatai" : "Új projekt"}</h2>
                <Form.Field>
                    <label>Projekt megnevezése:</label>
                    <Input
                        value={editedProject.label}
                        onChange={(ev) => this.injectSimpleChange("label", ev.target.value)}
                    />
                </Form.Field>
                <Form.Field>
                    <label>Státusz:</label>
                    <Dropdown
                        selection placeholder="Válasszon"
                        value={editedProject.status}
                        onChange={(ev, data) => this.injectSimpleChange("status", data.value)}
                        options={statusOptions}
                    />
                </Form.Field>
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
                    <label>Leírás:</label>
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
                {
                    this.props.projectId ? (
                        <p>
                            <Button color="red" onClick={() => this.delete()}>Projekt törlése</Button>
                        </p>
                    ) : null
                }
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
        if (this.props.projectId && Util.isEmptyObject(this.state.changesToPost)) {
            this.props.onSave();
        } else {
            if (this.props.projectId) {
                this.props.postUpdateProject(this.state.updates, this.state.changesToPost, this.props.projectId, this.props.onSave);
            } else {
                this.props.postCreateProject(this.state.changesToPost, this.props.onSave);
            }
        }
    }

    delete() {
        if (!this.props.projectId) {
            return;
        }

        this.props.showMessage("Biztos?", "Törlés", "warning", () => {
            this.props.deleteProject(this.props.projectId, this.props.onDelete);
        });
    }

})
