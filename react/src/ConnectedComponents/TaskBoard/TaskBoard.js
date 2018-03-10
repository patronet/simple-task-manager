import React from 'react';
import { connect } from 'react-redux'
import { Droppable, Draggable } from 'react-beautiful-dnd'
import { registerDropContainer } from '../../dnd'
import { reorderList, removeFromList, insertIntoList } from '../../dnd'
import { resetTaskList } from '../../redux/taskBoard/actions'

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
				return this.props.taskBoard.columns[columnName].items[index];
			}, (fromIndex, toIndex) => {
				var originalList = this.props.taskBoard.columns[columnName].items;
				var newList = reorderList(originalList, fromIndex, toIndex);
				this.props.resetTaskList(columnName, newList);
			}, (index) => {
				var originalList = this.props.taskBoard.columns[columnName].items;
				var newList = removeFromList(originalList, index);
				this.props.resetTaskList(columnName, newList);
			}, (index, item) => {
				var originalList = this.props.taskBoard.columns[columnName].items;
				var newList = insertIntoList(originalList, index, item);
				this.props.resetTaskList(columnName, newList);
			});
		}
		
	}
	
	  render() {
	    return (
	      <div>
            {this.props.taskBoard.columnOrder.map((columnName, index) => {
    			let targetName = "taskBoard:" + columnName;
            	return (
        		    <Droppable key={columnName} droppableId={targetName}>
      	                {(dropProvided, dropSnapshot) => (
          	                <div
          	                    ref={dropProvided.innerRef}
          	                    style={{background: dropSnapshot.isDraggingOver ? 'green' : 'grey'}}
          	                >
          	                	<h2>{columnName}</h2>
          	                	{this.props.taskBoard.columns[columnName].items.map((item, index) => (
      	                			<Draggable key={item.id} draggableId={"item-" + item.id} index={index}>
          	                			{(dragProvided, dragSnapshot) => (

          	                					<div>
          	                					<div
          	                					ref={dragProvided.innerRef}
          	                					{...dragProvided.draggableProps}
          	                					{...dragProvided.dragHandleProps}
          	                					style={{
          	                					userSelect: 'none',
          	                					padding: 20,
          	                					margin: '0 0 10px 0',
          	                					background: dragProvided.isDragging ? 'red' : 'blue',
          	                					...dragProvided.draggableProps.style,
          	                					}}
          	                					>
          	                					{item.content}
          	                					</div>
          	                					{dragProvided.placeholder}
          	                					</div>
  	                					)}
          	                		</Draggable>
              	                ))}
          	                </div>
      	                )}
        		    </Droppable>
    		    );
            })}
	      </div>
	    );
	  }
})
