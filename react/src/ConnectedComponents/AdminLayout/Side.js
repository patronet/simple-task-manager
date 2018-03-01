import React from 'react';
import { Menu, Icon, List } from 'semantic-ui-react'
import { connect } from 'react-redux'
import { moveToPage, setSidebarMinimized } from '../../redux/frame/actions'



export default connect(state => {
    return {
        side: state.frame.side
    };
}, dispatch => {
    return {
        moveToPage: (pageType, pageProperties = {}) => moveToPage(dispatch, pageType, pageProperties),
        setSidebarMinimized: (minimized) => setSidebarMinimized(dispatch, minimized),
    };
})(class extends React.Component {

    render() {
        let items = [];
        items.push(this.createMenuItem("dashboard", "dashboard", "Dashboard", () => this.props.moveToPage("dashboard")));
        items.push(this.createMenuItem("taskboard", "table", "Task Board", () => this.props.moveToPage("taskBoard")));
        items.push(this.createMenuItem("calendar", "calendar", "Naptár", () => this.props.moveToPage("calendar")));
        items.push(this.createMenuItem("issues", "bug", "Visszajelzések", () => alert("this is the issue tracker!")));
        items.push(this.createMenuItem("tasks", "tasks", "Feladatok", () => alert("this is the tasks list!")));
        items.push(this.createMenuItem("projects", "folder", "Projektek", () => this.props.moveToPage("projectList")));
        items.push(this.createMenuItem("customers", "address book", "Ügyfelek", () => alert("this is the customer address book!")));
        items.push(this.createMenuItem("users", "users", "Felhasználók", () => alert("this is the user manager!")));
        items.push(this.createMenuItem("tests", "checkmark", "Tesztek", () => alert("this is the test analyzer!")));

        items.push(this.createMenuItem(
            "toggle",
            this.props.side.minimized ? "chevron right" : "chevron left",
            this.props.side.minimized ? "Kinyit" : "Menü összecsukása",
            () => {
                this.props.setSidebarMinimized(!this.props.side.minimized);
            }
        ));

        return (
            <div>
                <Menu vertical fluid inverted>
                    {items}
                </Menu>
            </div>
        );
    }

    createMenuItem(name, icon, label, callback) {
        let color = (name == "toggle") ? "teal" : null;
        if (this.props.minimized) {
            return (
                <Menu.Item name={name} key={name} onClick={callback}>
                    <Icon name={icon} color={color} />
                </Menu.Item>
            );
        } else {
            return (
                <Menu.Item name={name} key={name} onClick={callback} style={{width:"200px"}}>
                    <Icon name={icon} /> {label}
                </Menu.Item>
            );
        }
    }

})
