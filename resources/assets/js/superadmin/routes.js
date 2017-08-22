import sharedRoutes from '_shared/routes';

const routes = [
  {
    path: '/users',
    name: 'users',
    meta: {
      showMenu: false,
    },
    component: require('_shared/pages/Users').default,
  },
];

export default sharedRoutes.concat(routes);
