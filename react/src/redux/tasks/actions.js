
export const resetTaskList = (dispatch, columnName, newList) => {
	dispatch({
		type: 'RESET_TASK_LIST',
		columnName, newList
	});
};

// TODO: taskMoved (ajax)
// TODO: taskMoveRevert => full reload?
