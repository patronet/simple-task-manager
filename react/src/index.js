import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import moment from 'moment';
import { DragDropContext } from 'react-beautiful-dnd';
import { updateStateOnDrop } from './dnd';
import BigCalendar from 'react-big-calendar';
import store from './store';
import { serializeSession } from './session';
import App from './App';

import 'react-big-calendar/lib/css/react-big-calendar.css';
import './index.css';

moment.locale('hu');
BigCalendar.momentLocalizer(moment);

window.addEventListener("beforeunload", function (event) {
    serializeSession(store.getState());
});

ReactDOM.render(
    <Provider store={store}>
    	<DragDropContext onDragEnd={(result) => {
    		if (result.source && result.destination && result.reason == 'DROP') {
    			updateStateOnDrop(
					result.source.droppableId, result.source.index,
					result.destination.droppableId, result.destination.index
				);
    		}
		}}>
        	<App />
	    </DragDropContext>
    </Provider>,
document.getElementById('root'));
