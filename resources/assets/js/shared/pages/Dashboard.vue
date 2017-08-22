<template>
  <div class="col-xs-12 col-md-10">

    <!-- BEGIN content head-->
    <div class="row">
      <div class="col-xs-12 col-md-5 col-md-offset-1">
        <div class="page-title">
          <i class="fa fa-bar-chart"></i> Statistics
        </div>
      </div>
    </div>
    <!-- END content head -->

    <!-- END content -->
    <div class="row">
      <div class="col-xs-12 col-md-5 col-md-offset-1">
        <dq-loader :loading="loading">
          <table class="table table-stripped stats">
            <tr v-if="Auth.isSuperadmin || Auth.hasPermission('manage_groups')">
              <td><router-link to="groups">{{ groupsCount }}</router-link></td>
              <td>{{ groupPluralized }}</td>
            </tr>
            <tr v-if="!Auth.isUser">
              <td><router-link to="users">{{ usersCount }}</router-link></td>
              <td>active {{ userPluralized }}</td>
            </tr>
            <tr v-if="Auth.isSuperadmin || Auth.hasPermission('manage_devices')">
              <td><router-link to="devices">{{ devicesCount }}</router-link></td>
              <td>{{ devicePluralized }}</td>
            </tr>
          </table>
        </dq-loader>
      </div>
    </div>
    <!-- END content -->

  </div>

</template>
<script>
import Promise from 'es6-promise';
import User from '_shared/resources/User';
import Group from '_shared/resources/Group';
import Device from '_shared/resources/Device';
import DqLoader from '_components/dqLoader';

export default {

  components: { DqLoader },

  created() {
    this.loadCounts();
  },

  data() {
    return {
      usersCount: 0,
      groupsCount: 0,
      devicesCount: 0,

      loading: false,
    };
  },

  computed: {
    userPluralized() {
      return 'user'.pluralize(this.usersCount);
    },

    groupPluralized() {
      return 'group'.pluralize(this.groupsCount);
    },

    devicePluralized() {
      return 'device'.pluralize(this.devicesCount);
    },
  },

  methods: {
    loadCounts() {
      const params = { onlyData: true, itemsPerPage: 1 };
      const responseHandler = response => response.data.meta.total;
      const promises = [];

      const usersPromise = this.Auth.isSuperadmin
        ? User.query(params).then(responseHandler)
        : 0;
      promises.push(usersPromise);

      const groupsPromise = this.Auth.hasPermission('manage_groups')
        ? Group.query(params).then(responseHandler)
        : 0;
      promises.push(groupsPromise);

      const devicesPromise = this.Auth.hasPermission('manage_devices')
        ? Device.query(params).then(responseHandler)
        : 0;
      promises.push(devicesPromise);

      this.loading = true;

      return Promise.all(promises).then((counts) => {
        const [usersCount, groupsCount, devicesCount] = counts;
        this.usersCount = usersCount;
        this.groupsCount = groupsCount;
        this.devicesCount = devicesCount;
        this.loading = false;
      });
    },
  },
};
</script>
<style>
  .stats td {
    font-size: 16px;
  }
</style>
