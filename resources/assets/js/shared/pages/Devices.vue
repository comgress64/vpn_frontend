<template>
  <div class="col-xs-12 col-md-10">

    <!-- BEGIN content head-->
    <div class="row">
      <div class="col-xs-12">
        <div class="page-title">
          Devices
        </div>
        <div class="breadcrumbs">
          <a href="index.html"><i class="fa fa-home"></i>Home</a>
          <span>Devices</span>
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
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="filter_id">ID</label>
                  <input type="text" class="form-control" v-model="filter.itemId">
                </div>
                <div class="form-group" v-if="Auth.isSuperuser">
                  <label for="filter_creator">Owner</label>
                  <select class="form-control" v-model="filter.userId">
                    <option>Any (default)</option>
                    <option v-for="user in meta.users" :value="user.id">{{ user.fullname }}</option>
                  </select>
                </div>
              </div>
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="filter_name">Name</label>
                  <input type="text" class="form-control" v-model="filter.name">
                </div>
                <div class="form-group">
                  <label for="filter_comment">Comment</label>
                  <input type="text" class="form-control" v-model="filter.comment">
                </div>
              </div>
              <div class="col-xs-4">
                <div class="form-group">
                  <label>Group</label>
                  <select class="form-control" v-model="filter.groupId">
                    <option>Any (default)</option>
                    <option v-for="group in meta.groups" :value="group.id">{{ group.name }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="filter_ip">IP</label>
                  <input type="text" class="form-control" v-model="filter.ip">
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
              <a href="#" @click="newItem()" class="btn btn-success btn-md btn-icon" :class="{ disabled: !Auth.user.canCreateDevices }">
                <i class="fa fa-plus-circle"></i> New device
              </a>
              <error-messages
                v-if="!Auth.user.canCreateDevices"
                class="max-devices-error"
                :errors="['You cannot create more than ' + Auth.user.maxDevices + ' ' + devicePluralized]"
              ></error-messages>
            </div>
          </div>
        </div>

        <div class="col-xs-12">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th></th>
                <th>ID</th>
                <th>Name</th>
                <th>Group</th>
                <th>User Name</th>
                <th>Type</th>
                <th>Creator</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in data">
                <td>
                  <a href="#" @click.prevent="edit(item)">
                    <i class="fa fa-pencil"></i>
                  </a>
                </td>
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>
                  <span v-if="item.group != null">{{ item.group.name }}</span>
                  <span v-else>---</span>
                </td>
                <td>
                  <span v-if="item.user != null">{{ item.user.fullname }}</span>
                  <span v-else>---</span>
                </td>
                <td>
                  <span v-if="item.user != null">User</span>
                  <span v-else>Device</span>
                </td>
                <td>
                  <span v-if="item.creator != null">{{ item.creator.fullname }}</span>
                  <span v-else>---</span>
                </td>
                <td>
                  <a :href="getDeviceHref(item)" target="_blank">{{ item.ip }}</a>
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

    <vudal name="deviceForm">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id != null">Edit device</h4>
        <h4 class="modal-title" v-else>Add device</h4>
      </div>
      <div class="modal-body contents">
        <div class="form-group">
          <label>Group</label>
          <select class="form-control" v-model="current.groupId">
            <option v-for="group in meta.groups" :value="group.id">{{ group.name }}</option>
          </select>
          <error-messages :errors="errors.groupId"></error-messages>
        </div>
        <div class="form-group">
          <label for="add_name">Name</label>
          <input type="text" class="form-control" v-model="current.name">
          <error-messages :errors="errors.name"></error-messages>
        </div>
        <div class="form-group">
          <label for="add_comment">Comment</label>
          <input type="text" class="form-control" v-model="current.comment">
          <error-messages :errors="errors.comment"></error-messages>
        </div>
        <div class="form-group">
          <input type="checkbox" v-model="current.isolated">
          <label>Isolated</label>
        </div>
        <div class="form-group" v-if="current.id != null">
          <label for="add_ip">IP</label>
          <input type="text" class="form-control" readonly="readonly" v-model="current.ip">
        </div>
        <div class="form-group" v-if="current.id != null">
          <label for="add_status">Current status</label>
          <input type="text" class="form-control" readonly="readonly" v-model="current.status">
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
import DqLoader from '_components/dqLoader';
import hasClosableFilter from '_shared/mixins/hasClosableFilter';
import rest from '_shared/mixins/rest';
import Device from '_shared/resources/Device';
import Vudal from 'vudal';
import ErrorMessages from '_components/errorMessages';
import Pager from '_components/bsPager';
import catcherNotifier from '_shared/services/catcherNotifier';

export default {

  components: { DqLoader, Pager, ErrorMessages, Vudal },

  mixins: [rest(Device), hasClosableFilter],

  created() {
    this.$on('editing', () => {
      this.getStatus();
    });
  },

  data() {
    return {
      defaultParams: {
        with: 'group,user,creator',
      },

      newResourceData: {
        isolated: true,
      },
    };
  },

  computed: {
    devicePluralized() {
      return 'device'.pluralize(this.Auth.user.maxDevices);
    },
  },

  methods: {
    downloadKey() {
      location.href = `/devices/${this.current.id}/download_key`;
    },

    getStatus() {
      Device.getStatus({ id: this.current.id }).then((response) => {
        this.$set(this.current, 'status', response.data.status);
      }).catch(catcherNotifier);
    },

    getDeviceHref(device) {
      return `http://[${device.ip}]`;
    },
  },
};
</script>
<style>
  .max-devices-error {
    font-size: 16px;
  }
</style>
