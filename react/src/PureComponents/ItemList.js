import React from 'react';
import { Input, Menu } from 'semantic-ui-react';
import { fetchProjects } from '../actions/projects.js';

export default class extends React.Component {

    render() {
        return (<div>
            <p>Test string: {this.props.projects.length} {this.props.isFetching ? "Betöltés..." : ""}</p>
        </div>);
    }

}
