import VueRouter from 'vue-router';
import Auth from '_shared/services/Auth';
import objectAssign from 'object-assign';

export default function (params) {
  const options = objectAssign({
    hashbang: false,
    history: true,
    mode: 'history',
  }, params);

  const router = new VueRouter(options);

  router.beforeEach((to, from, next) => {
    // if user not authenticated then go to login page
    Auth.getAuthenticatedUser().then((user) => {
      if (!user) {
        location.href = '/auth';
      }
      else if (to.meta != null && to.meta.permission != null &&
        !Auth.hasPermission(to.meta.permission)
      ) {
        location.href = '/';
      }
      else {
        next();
      }
    }).catch((error) => {
      console.error(error);
    });
  });

  return router;
}
