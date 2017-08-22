import Vue from 'vue';

export default Vue.filter('valueOfArray', (value, array, field) => {
  if (!array || array == null) return null;
  const foundItem = array.filter({ id: value }).first();
  if (foundItem) {
    return foundItem[field];
  }
  return null;
});
