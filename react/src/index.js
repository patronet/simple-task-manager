import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { DragDropContext } from 'react-beautiful-dnd';
import store from './store';
import { updateStateOnDrop } from './dnd';
import App from './App';
import BigCalendar from 'react-big-calendar';
import moment from 'moment';

import 'react-big-calendar/lib/css/react-big-calendar.css';
import './index.css';

moment.locale('hu');
BigCalendar.momentLocalizer(moment);

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
