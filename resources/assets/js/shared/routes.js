const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: require('_shared/pages/Dashboard').default,
  },
  {
    path: '/license',
    name: 'license',
    component: require('_shared/pages/License').default,
  },
  {
    path: '/devices',
    name: 'devices',
    meta: {
      permission: 'manage_devices',
    },
    component: require('_shared/pages/Devices').default,
  },
  {
    path: '/groups',
    name: 'groups',
    meta: {
      permission: 'manage_groups',
    },
    component: require('_shared/pages/Groups').default,
  },
];

export default routes;

