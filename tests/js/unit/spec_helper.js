import Vue from 'vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource';
import 'jasmine-ajax';
import 'dq/lib/mySugar';
import '_semantic';
import '_shared/mixins/Auth';
import 'dq/lib/calendar';
import DqModal from 'dq/plugins/modal';

const requireAll = require.context('_shared/filters', false, /.js$/);
requireAll.keys().forEach(requireAll);

Vue.use(VueRouter);
Vue.use(VueResource);
Vue.use(DqModal);

Vue.config.devtools = false;

jasmine.Ajax.install();
