import React from 'react';
import { List } from 'semantic-ui-react'

export default class extends React.Component {

    render() {
        return (
            <div>
                <List>
                    <List.Item as="a">Projektek</List.Item>
                    <List.Item><a href="#">XYZ</a></List.Item>
                </List>
            </div>
        )
    }

}
