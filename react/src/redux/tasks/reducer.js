import update from 'react-addons-update';

export default (state = {}, action = null) => {
    if (action.type == 'RESET_TASK_LIST') {
    	return update(state, {
            taskBoard: {
        		columns: {
        			[action.columnName]: {
        				tasks: {$set: action.newList},
        			}
        		},
            },
		});
    } else {
        return state;
    }
}
