import Vue from 'vue';
import { Promise } from 'es6-promise';

export default {

  // authentication errors
  errors: {
    login: '',
    reg: '',
  },

  // current user data
  user: {},

  getAuthenticatedUser() {
    return new Promise((resolve) => {
      Vue.http.get('/auth/check', { showProgressBar: false }).then((response) => {
        this.user = response.body;
        resolve(this.user);
      }, () => {
        resolve(null);
      });
    });
  },

  get isAuthenticated() {
    return !Object.isEmpty(this.user);
  },

  get isSuperadmin() {
    return this.user.role === 'superadmin';
  },

  get isAdmin() {
    return this.user.role === 'admin';
  },

  get isUser() {
    return this.user.role === 'user';
  },

  hasPermission(permission) {
    return !Object.isEmpty(this.user) &&
      (this.user.permissions.includes(permission) || this.isSuperadmin);
  },

  login(email, password, redirect = false) {
    this.errors.login = '';

    Vue.http.post('/auth/login', { email, password }).then((response) => {
      if (response.data.success) {
        if (redirect) {
          location.href = redirect;
        }
      }
    }, (response) => {
      this.errors.login = response.data.error;
    });
  },

  register(data) {
    return Vue.http.post('/auth/register', data);
  },

  logout(redirect = false) {
    Vue.http.get('/auth/logout').then(() => {
      this.user = {};
      if (redirect) {
        location.href = redirect;
      }
    });
  },
};
