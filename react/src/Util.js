
export default {

    isEmptyObject(object) {
        for (var key in object) {
            if (object.hasOwnProperty(key)) {
                return false;
            }
        }
        return true;
    },

    uniqidIndex: 0,
    uniqid() {
        return "id" + (++this.uniqidIndex);
    }

}
