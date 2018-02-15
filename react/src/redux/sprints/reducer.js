import update from 'react-addons-update';

// TODO
export default (state = [], action) => {
    if (action.type == 'REQUEST_SPRINTS') {
        return update(state, {mainSprintList: {isFetching: {$set: true}}});
    } else if (action.type == 'RECEIVE_SPRINTS') {
        let sprintsUpdates = {};
        let sprintIds = [];
        for (let i = 0; i < action.sprints.length; i++) {
            let sprintContainer = action.sprints[i];
            let sprint = sprintContainer.sprint;
            let sprintId = sprint.sprint_id;
            sprintsUpdates[sprintId] = {$set: sprintContainer};
            sprintIds.push(sprintId);
        }
        return update(state, {
            mainSprintList: {
                isFetching: {$set: false},
                sprintIds: {$set: sprintIds},
                pageCount: {$set: action.pageCount},
                pageNo: {$set: action.pageNo}
            },
            sprints: sprintsUpdates
        });
    } else {
        return state;
    }
};
