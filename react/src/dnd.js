
let containers = {};

export const registerDropContainer = (name, getByIndex, move, remove, insert) => {
	containers[name] = {name, getByIndex, move, remove, insert};
};

export const updateStateOnDrop = (sourceName, sourceIndex, targetName, targetIndex) => {
	let sourceContainer = containers[sourceName];
	if (!sourceContainer) {
		return;
	}
	
	if (sourceName == targetName) {
		sourceContainer.move(sourceIndex, targetIndex);
	} else {
		let targetContainer = containers[targetName];
		if (!targetContainer) {
			return;
		}

		let item = sourceContainer.getByIndex(sourceIndex);
		if (!item) {
			return;
		}

		sourceContainer.remove(sourceIndex);
		targetContainer.insert(targetIndex, item);
	}
}

export const reorderList = (list, fromIndex, toIndex) => {
	const newList = Array.from(list);
	const [movingItem] = newList.splice(fromIndex, 1);
	newList.splice(toIndex, 0, movingItem);
	return newList;
}

export const removeFromList = (list, index) => {
	const newList = Array.from(list);
	newList.splice(index, 1);
	return newList;
}

export const insertIntoList = (list, index, item) => {
	const newList = Array.from(list);
	newList.splice(index, 0, item);
	return newList;
}
