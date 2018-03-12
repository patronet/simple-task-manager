import React from 'react';
import { connect } from 'react-redux';
import moment from 'moment';
import BigCalendar from 'react-big-calendar';
import { Button, Icon } from 'semantic-ui-react';

export default connect(state => {
    return {
        // ...
    };
}, dispatch => {
    return {
        // ...
    };
})(class extends React.Component {

    componentWillMount() {
        // TODO: load dashboard data
    }

    render() {
        let activeProjectCount = 2;
        let baseTime = moment().set({'hour': 11, 'minute': 0, 'second': 0, 'millisecond': 0})
        return (
            <div>
                <div>
                    <div style={{float:"right"}}>
                        <Button>
                            <Icon name="add" />
                            Új önálló esemény
                        </Button>
                    </div>
                    <h2>Naptár</h2>
                </div>
                <p>
                    {new Date().toDateString()}
                </p>
                <BigCalendar
                    events={[
                        {id: 1, title: "Esemény 1", start: this.rt(-1, 10), end: this.rt(-1, 11, 15)},
                        {id: 2, title: "Esemény 2", start: this.rt(0, 9), end: this.rt(0, 10)},
                        {id: 3, title: "Esemény 3", start: this.rt(0, 11), end: this.rt(0, 13)},
                        {id: 4, title: "Esemény 4", start: this.rt(2, 12), end: this.rt(2, 13, 30)},
                        {id: 5, title: "Esemény 5", start: this.rt(2, 14, 15), end: this.rt(2, 15, 45)},
                        {id: 5, title: "Esemény 5", start: this.rt(2, 16, 0), end: this.rt(2, 18, 20)},
                    ]}
                    defaultDate={moment().toDate()}
                    defaultView="week"
                />
            </div>
        );
    }

    rt(daysToMove = 0, hour = 0, minute = 0, second = 0, millisecond = 0) {
        return moment().add(daysToMove, "days").set({hour, minute, second, millisecond}).toDate();
    }

})
