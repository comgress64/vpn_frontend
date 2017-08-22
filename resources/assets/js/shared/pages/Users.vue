<template>
  <div class="col-xs-12 col-md-10">

    <!-- BEGIN content head-->
    <div class="row">
      <div class="col-xs-12">
        <div class="page-title">
          Users
        </div>
        <div class="breadcrumbs">
          <router-link :to="{ name: 'dashboard' }"><i class="fa fa-home"></i>Home</router-link>
          <span>Users</span>
        </div>
      </div>
    </div>
    <!-- END content head-->

    <!-- BEGIN filter -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box icon">
          <div class="box-head" @click="toggleFilter()">
            <i class="fa fa-search"></i>Filter
            <div class="actions pull-right">
              <a class="btn btn-default btn-xs btn-collapse">
                <i class="fa" :class="{'fa-minus': filterOpened, 'fa-plus': !filterOpened}"></i>
              </a>
            </div>
          </div>
          <div class="box-body" v-if="filterOpened">
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label for="filter_name">Name</label>
                  <input type="text" class="form-control" v-model="filter.name">
                </div>
                <div class="form-group">
                  <label for="filter_email">E-mail</label>
                  <input type="text" class="form-control" v-model="filter.email">
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <label>Group</label>
                  <select class="form-control" v-model="filter.groupId">
                    <option>Any (default)</option>
                    <option v-for="group in meta.groups" :value="group.id">{{ group.name }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Role</label>
                  <select class="form-control" v-model="filter.role">
                    <option>Any (default)</option>
                    <option value="superadmin">Superadmin</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12">
                <div class="page-actions pull-right">
                  <button type="button" class="btn btn-default btn-md btn-icon" @click="clearFilter()">
                    <i class="fa fa-close"></i>Clear
                  </button>
                  <button type="button" class="btn btn-primary btn-md btn-icon" @click="getList()">
                    <i class="fa fa-search"></i>Filter
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END filter -->

    <!-- BEGIN content-->
    <div class="row">

      <dq-loader :loading="loading">
        <div class="col-xs-12">
          <div id="matching-results" class="matching-results">
            Number of Matching Records: <span class="matching-results__number">{{ meta.total }}</span>
          </div>
        </div>

        <div class="col-xs-12">
          <div class="page-title">
            <div class="actions">
              <a href="#" @click="newItem()" class="btn btn-success btn-md btn-icon"><i class="fa fa-plus-circle"></i> New user</a>
            </div>
          </div>
        </div>

        <div class="col-xs-12">
          <table id="usersTable" class="table table-stripped">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Phone</th>
                <th>E-mail</th>
                <th>Group</th>
                <th>Role</th>
                <th>Devices</th>
                <th>Device IP</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in data">
                <td>
                  <a href="#" @click.prevent="edit(item)">
                    <i class="fa fa-pencil"></i>
                  </a>
                </td>
                <td>{{ item.fullname }}</td>
                <td>{{ item.phone }}</td>
                <td>{{ item.email }}</td>
                <td>
                  <span v-if="item.role === 'superadmin'">---</span>
                  <span v-else>{{ item.groups | listOfGroups }}</span>
                </td>
                <td>{{ item.role.capitalize() }}</td>
                <td>{{ item.createdDevices.length }}/{{ item.noDevicesRestriction ? '&infin;' : item.maxDevices }}</td>
                <td>
                  <a v-if="item.ownDevice != null" :href="getDeviceHref(item.ownDevice)" target="_blank">{{ item.ownDevice.ip }}</a>
                </td>
              </tr>
            </tbody>
          </table>

          <pager
            :total-items="meta.total"
            :current-page="currentPage"
            :page-size="itemsPerPage"
            @page-changed="onPageChanged"
            ></pager>
        </div>
      </dq-loader>

    </div>
    <!-- END content-->
    <vudal name="userForm">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id != null">Edit user</h4>
        <h4 class="modal-title" v-else>Add user</h4>
      </div>
      <div class="modal-body contents">
        <div class="form-group">
          <label for="edit_firstName">First Name</label>
          <input type="text" class="form-control" v-model="current.fname">
          <error-messages :errors="errors.fname"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_lastName">Last Name</label>
          <input type="text" class="form-control" v-model="current.lname">
          <error-messages :errors="errors.lname"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_phone">Phone</label>
          <input type="text" class="form-control" v-model="current.phone">
          <error-messages :errors="errors.phone"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_email">E-mail</label>
          <input type="text" class="form-control" v-model="current.email">
          <error-messages :errors="errors.email"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_email">Password</label>
          <input type="password" class="form-control" v-model="current.password">
          <error-messages :errors="errors.password"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_email">Confirm password</label>
          <input type="password" class="form-control" v-model="current.passwordConfirmation">
          <error-messages :errors="errors.passwordConfirmation"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_role">Role</label>
          <select class="form-control" v-model="current.role">
            <option value="superadmin">Super Admin</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
          <error-messages :errors="errors.role"></error-messages>
        </div>
        <div class="form-group" v-if="current.groupIds != null && current.role !== 'superadmin'">
          <label for="edit_group">Groups</label>
          <select multiple class="form-control" v-model="current.groupIds">
            <option :value="group.id" v-for="group in meta.groups">{{ group.name }}</option>
          </select>
          <error-messages :errors="errors.groupIds"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_maxDevices">Max. devices available</label>
          <input type="text" class="form-control" v-model="current.maxDevices" :disabled="current.noDevicesRestriction">
          <error-messages :errors="errors.maxDevices"></error-messages>
        </div>
        <div class="form-group">
          <input type="checkbox" id="edit_noRestrictions" v-model="current.noDevicesRestriction" @change="onNoDevicesRestrictionUpdate">
          <label for="edit_noRestrictions">No restrictions</label>
        </div>
        <div class="form-group" v-if="current.role !== 'superadmin'">
          <p><strong>Permissions</strong></p>
          <div class="row">
            <div class="col-xs-4">
              <input type="checkbox" id="edit_permissionLoginToWebInterface" v-model="current.permissions" value="login">
              <label for="edit_permissionLoginToWebInterface">Login to web interface</label>
            </div>
            <div class="col-xs-4">
              <input type="checkbox" id="edit_permissionManageGroups" v-model="current.permissions" value="manage_groups">
              <label for="edit_permissionManageGroups">Manage groups</label>
            </div>
            <div class="col-xs-4">
              <input type="checkbox" id="edit_permissionManageDevices" v-model="current.permissions" value="manage_devices">
              <label for="edit_permissionManageDevices">Manage devices</label>
            </div>
          </div>
        </div>
        <div class="form-group" v-if="current.id != null">
          <p>
            Get VPN Key: <a href="#" @click.prevent="downloadKey()">download</a>
          </p>
        </div>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-danger pull-left" v-if="current.id != null" @click="destroy(current)">Remove</button>
        <button type="button" class="btn btn-default" @click="restoreOriginal()">Reset</button>
        <button type="button" class="btn btn-default cancel">Close</button>
        <button type="button" class="btn btn-primary" @click="save()">Save</button>
      </div>
    </vudal>

  </div>

</template>
<script>
import rest from '_shared/mixins/rest';
import hasClosableFilter from '_shared/mixins/hasClosableFilter';
import User from '_shared/resources/User';
import Vudal from 'vudal';
import ErrorMessages from '_components/errorMessages';
import Pager from '_components/bsPager';
import DqLoader from '_components/dqLoader';

export default {

  mixins: [rest(User), hasClosableFilter],

  components: { Vudal, ErrorMessages, Pager, DqLoader },

  filters: {
    listOfGroups(groups) {
      return groups.map(group => group.name).join(', ');
    },
  },

  created() {
    this.$on('editing', () => {
      if (this.current.noDevicesRestriction) {
        this.current.maxDevices = null;
      }
    });
  },

  data() {
    return {
      defaultParams: {
        with: 'groups,createdDevices,ownDevice',
      },

      newResourceData: {
        role: 'user',
        maxDevices: 0,
        groupIds: [],
        permissions: [],
      },
    };
  },

  methods: {
    downloadKey() {
      location.href = `/users/${this.current.id}/download_key`;
    },

    onNoDevicesRestrictionUpdate($event) {
      const checked = $event.target.checked;
      this.current.maxDevices = checked ? null : 0;
    },

    getDeviceHref(device) {
      return `http://[${device.ip}]`;
    },

  },
};
</script>
