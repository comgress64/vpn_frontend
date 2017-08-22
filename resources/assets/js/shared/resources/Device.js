import Vue from 'vue';

const resource = Vue.resource('/api/devices{/id}', {}, {
  getStatus: { method: 'GET', url: '/api/devices{/id}/status' },
});
resource.name = 'device';
export default resource;
