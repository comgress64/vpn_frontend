import Vue from 'vue';

const resource = Vue.resource('/api/users{/id}', {}, {});
resource.name = 'user';
export default resource;
