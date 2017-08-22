import Vue from 'vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource';
import VueResourceCaseConverter from 'vue-resource-case-converter';
import VueProgressBar from 'vue-progressbar';
import VueResourceProgressBarInterceptor from 'vue-resource-progressbar-interceptor';
import { VudalPlugin } from 'vudal';

import '_shared/lib/mySugar';
import '_shared/mixins/Auth';

const requireAll = require.context('_shared/filters', false, /.js$/);
requireAll.keys().forEach(requireAll);

Vue.use(VudalPlugin, {
  hideModalsOnDimmerClick: false,
});
Vue.use(VueRouter);
Vue.use(VueResource);
Vue.use(VueProgressBar, {
  color: '#29d',
  height: '3px',
});
Vue.use(VueResourceCaseConverter, {
  responseUrlFilter(url) {
    return url.indexOf('api') >= 0 || url.indexOf('auth') >= 0;
  },
});
Vue.use(VueResourceProgressBarInterceptor);
window.Vue = Vue;

Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

const apiTokenMetaElement = document.querySelector('#api_token');
if (apiTokenMetaElement !== null) {
  Vue.http.headers.common.Authorization =
    `Bearer ${apiTokenMetaElement.getAttribute('value')}`;
}

const gitRevMetaElement = document.querySelector('#git_rev');
Vue.gitRevision = gitRevMetaElement !== null ? gitRevMetaElement.getAttribute('value') : '';

const versionMetaElement = document.querySelector('#version');
Vue.appVersion = versionMetaElement !== null ? versionMetaElement.getAttribute('value') : '';

const envMetaElement = document.querySelector('#env');
Vue.env = envMetaElement !== null ? envMetaElement.getAttribute('value') : 'production';

const projectNameMetaElement = document.querySelector('#project_name');
Vue.projectName = projectNameMetaElement !== null ? projectNameMetaElement.getAttribute('value') : 'Project name';
