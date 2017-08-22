import Vue from 'vue';
import VueRouter from 'vue-router';

import '_shared/styles';

// Include shared app configuration
import '_shared/app';

import Root from '_auth/pages/Root';

const router = new VueRouter({
  routes: [
    {
      path: '/',
      redirect: '/sign_in',
    },
    {
      path: '/sign_in',
      name: 'signIn',
      component: require('_auth/pages/SignIn').default,
    },
  ],
});

new Vue({
  router,
  render(h) {
    return h(Root);
  },
}).$mount('#app');
