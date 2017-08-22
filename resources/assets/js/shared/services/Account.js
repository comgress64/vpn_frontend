import Vue from 'vue';

export default {
  update(data) {
    return Vue.http.put('/api/account', data).then(response => response.body.data);
  },
};
