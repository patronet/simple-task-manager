import React from 'react';
import { connect } from 'react-redux'
import { Droppable, Draggable } from 'react-beautiful-dnd'
import { registerDropContainer } from '../../dnd'
import { reorderList, removeFromList, insertIntoList } from '../../dnd'
import { resetTaskList } from '../../redux/taskBoard/actions'

import './taskboard.css'

export default connect(state => {
    return {
        taskBoard: state.taskBoard,
    };
}, dispatch => {
    return {
    	resetTaskList: (columnName, newList) => resetTaskList(dispatch, columnName, newList),
    };
})(class extends React.Component {

	constructor(props) {
		super(props);
		for (let columnName of this.props.taskBoard.columnOrder) {
			let columnData = this.props.taskBoard.columns[columnName];
			let targetName = "taskBoard:" + columnName;
			registerDropContainer(targetName, (index) => {
				return this.props.taskBoard.columns[columnName].tasks[index];
			}, (fromIndex, toIndex) => {
				var originalList = this.props.taskBoard.columns[columnName].tasks;
				var newList = reorderList(originalList, fromIndex, toIndex);
				this.props.resetTaskList(columnName, newList);
			}, (index) => {
				var originalList = this.props.taskBoard.columns[columnName].tasks;
				var newList = removeFromList(originalList, index);
				this.props.resetTaskList(columnName, newList);
			}, (index, item) => {
				var originalList = this.props.taskBoard.columns[columnName].tasks;
				var newList = insertIntoList(originalList, index, item);
				this.props.resetTaskList(columnName, newList);
			});
		}

	}

	render() {
	    return (
            <div>

                <h2>Task Board</h2>

        		<div className="taskboard-board">
                    <div className="taskboard-board-inner">
        			    {this.props.taskBoard.columnOrder.map((columnName, index) => {
        					let targetName = "taskBoard:" + columnName;
        					let columnData = this.props.taskBoard.columns[columnName];
        			    	return (
        		    			<div className="taskboard-board-column" style={{backgroundColor:columnData.columnColor}}>
          	                		<h3 className="taskboard-board-column-title" style={{backgroundColor:columnData.titleColor}}>{columnData.title}</h3>
        						    <Droppable key={columnName} droppableId={targetName}>
        				                {(dropProvided, dropSnapshot) => (
        				  	                <div ref={dropProvided.innerRef} className="taskboard-board-column-list">
        				  	                	{columnData.tasks.map((task, index) => (
        				                			<Draggable key={"task-" + task.id} draggableId={"task-" + task.id} index={index}>
        				  	                			{(dragProvided, dragSnapshot) => (

        													<div className="taskboard-board-item-holder">
        				  	                					<div className="taskboard-board-item"
        															ref={dragProvided.innerRef} {...dragProvided.draggableProps} {...dragProvided.dragHandleProps}
        				  	                						style={{userSelect: 'none', ...dragProvided.draggableProps.style, borderColor: task.color}}
        			      	                					>
        				  	                						<h4 className="taskboard-board-item-title" style={{backgroundColor:task.color}}>{task.label}</h4>
        			      	                						<p style={{margin:"0.2vw"}}>
        			      	                							{task.projektLabel};
        			      	          									{task.customerLabel}
        			      	          								</p>
        			      	                					</div>
        			      	                					{dragProvided.placeholder}
        													</div>

        			                					)}
        			      	                		</Draggable>
        			          	                ))}
        			      	                </div>
        			  	                )}
        			    		    </Droppable>
        		    		    </div>
        				    );
        		        })}
        		    </div>
                </div>
            </div>
	    );
	}
})
