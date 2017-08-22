import Vue from 'vue';

const resource = Vue.resource('/api/groups{/id}', {}, {});
resource.name = 'group';
export default resource;
