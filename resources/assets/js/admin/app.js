import Vue from 'vue';

import '_shared/styles';

// Include shared app configuration
import '_shared/app';

import initRouter from '_shared/router';

import routes from '_admin/routes';

import Root from '_shared/pages/Root';

const router = initRouter({
  routes,
});

new Vue({
  router,
  render(h) {
    return h(Root);
  },
}).$mount('#app');
