import React from 'react';
import ProjectList from '../ConnectedComponents/ProjectList';
import SprintList from '../ConnectedComponents/SprintList';

export default class extends React.Component {

    render() {
        // XXX
        return (
            <div>
                <ProjectList />
                <hr style={{margin:"30px 0"}} />
                <SprintList />
            </div>
        );
    }

}
