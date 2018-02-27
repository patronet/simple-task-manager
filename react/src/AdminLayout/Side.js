import React from 'react';
import { Menu, Icon, List } from 'semantic-ui-react'

export default class extends React.Component {

    render() {
        return (
            <div style={{"overflow":"hidden"}}>
                <Menu vertical inverted>
                    <Menu.Item name="dashboard" icons="left" onClick={() => {}}>
                        <Icon name="dashboard" position="left" /> Dashboard
                    </Menu.Item>
                    <Menu.Item name="notifications" icons="left" onClick={() => {}}>
                        <Icon name="alarm" position="left" /> Értesítések
                    </Menu.Item>
                    <Menu.Item name="taskboard" icons="left" onClick={() => {}}>
                        <Icon name="table" position="left" /> Task Board
                    </Menu.Item>
                    <Menu.Item name="calendar" icons="left" onClick={() => {}}>
                        <Icon name="calendar" position="left" /> Naptár
                    </Menu.Item>
                    <Menu.Item name="issues" icons="left" onClick={() => {}}>
                        <Icon name="bug" position="left" /> Visszajelzések
                    </Menu.Item>
                    <Menu.Item name="tasks" icons="left" onClick={() => {}}>
                        <Icon name="tasks" position="left" /> Feladatok
                    </Menu.Item>
                    <Menu.Item name="projects" icons="left" onClick={() => {}}>
                        <Icon name="folder open" position="left" /> Projektek
                    </Menu.Item>
                    <Menu.Item name="customers" icons="left" onClick={() => {}}>
                        <Icon name="address book" position="left" /> Ügyfelek
                    </Menu.Item>
                    <Menu.Item name="users" icons="left" onClick={() => {}}>
                        <Icon name="users" position="left" /> Felhasználók
                    </Menu.Item>
                    <Menu.Item name="tests" icons="left" onClick={() => {}}>
                        <Icon name="checkmark" position="left" /> Tesztek
                    </Menu.Item>
                </Menu>
            </div>
        )
    }

}
