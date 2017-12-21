import React from 'react';
import { Input, Menu } from 'semantic-ui-react';

export default class extends React.Component {

    render() {
        var activeItem = "home";
        return (
            <Menu inverted secondary style={{backgroundColor:"#553300"}}>
                <Menu.Item name='home' active={activeItem === 'home'} onClick={this.handleItemClick} />
                <Menu.Item name='messages' active={activeItem === 'messages'} onClick={this.handleItemClick} />
                <Menu.Item name='friends' active={activeItem === 'friends'} onClick={this.handleItemClick} />
                <Menu.Menu position='right'>
                <Menu.Item>
                    <Input icon='search' placeholder='Search...' />
                </Menu.Item>
                <Menu.Item name='logout' active={activeItem === 'logout'} onClick={this.handleItemClick} />
                </Menu.Menu>
            </Menu>
        )
    }

    handleItemClick() {
        alert("!");
    }

}
