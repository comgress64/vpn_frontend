<template>
  <div class="col-xs-12 col-md-10">

    <!-- BEGIN content head-->
    <div class="row">
      <div class="col-xs-12">
        <div class="page-title">
          Groups
        </div>
        <div class="breadcrumbs">
          <router-link :to="{ name: 'dashboard' }"><i class="fa fa-home"></i>Home</router-link>
          <span>Groups</span>
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
                  <label for="filter_name">Name</label>
                  <input type="text" class="form-control" v-model="filter.name">
                </div>
                <div class="form-group">
                  <label for="filter_email">E-mail</label>
                  <input type="text" class="form-control" v-model="filter.email">
                </div>
              </div>
              <div class="col-xs-4">
                <div class="form-group">
                  <label for="filter_address">Address</label>
                  <input type="text" class="form-control" v-model="filter.address">
                </div>
                <div class="form-group">
                  <label for="filter_comment">Comment</label>
                  <input type="text" class="form-control" v-model="filter.comment">
                </div>
              </div>
              <div class="col-xs-4">
                <div class="form-group">
                  <label>IP</label>
                  <select class="form-control" v-model="filter.ip">
                    <option>Any (default)</option>
                    <option>127.0.0.1</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Ports</label>
                  <select class="form-control" v-model="filter.port">
                    <option>Any (default)</option>
                    <option v-for="port in meta.ports">{{ port }}</option>
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
              <a href="#" @click="newItem()" class="btn btn-success btn-md btn-icon"><i class="fa fa-plus-circle"></i> New group</a>
            </div>
          </div>
        </div>

        <div class="col-xs-12">
          <table class="table table-stripped">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Phone</th>
                <th>E-mail</th>
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
                <td>{{ item.name }}</td>
                <td>{{ item.phone }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.ip }}</td>
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

    <vudal name="groupForm">
      <div class="header">
        <button type="button" class="close">&times;</button>
        <h4 class="modal-title" v-if="current.id != null">Edit group</h4>
        <h4 class="modal-title" v-else>Add group</h4>
      </div>
      <div class="modal-body contents">
        <div class="form-group">
          <label for="edit_name">Name</label>
          <input type="text" class="form-control" v-model="current.name">
          <error-messages :errors="errors.name"></error-messages>
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
          <label for="edit_address">Address</label>
          <input type="text" class="form-control" v-model="current.address">
          <error-messages :errors="errors.address"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_comment">Comment</label>
          <input type="text" class="form-control" v-model="current.comment">
          <error-messages :errors="errors.comment"></error-messages>
        </div>
        <div class="form-group">
          <label for="edit_ports">List of ports opened on devices (all opened if nothing is selected)</label>
          <multiselect
            v-if="current.ports != null"
            v-model="current.ports"
            :options="current.ports"
            :multiple="true"
            :taggable="true"
            tag-placeholder="Add this as new port"
            placeholder="Add port"
            @tag="addPort"
          ></multiselect>
          <error-messages v-if="errors != null" :errors="errors.ports"></error-messages>
        </div>
        <div class="form-group" v-if="current.id != null">
          <label for="edit_ipRange">IP</label>
          <input type="text" class="form-control" readonly="readonly" v-model="current.ip">
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
import Group from '_shared/resources/Group';
import Vudal from 'vudal';
import Multiselect from 'vue-multiselect';
import ErrorMessages from '_components/errorMessages';
import Pager from '_components/bsPager';
import { notyError } from '_shared/services/Notifications';

export default {

  components: { DqLoader, Pager, ErrorMessages, Vudal, Multiselect },

  mixins: [rest(Group), hasClosableFilter],

  data() {
    return {
      defaultParams: {
        with: 'groupPorts',
      },

      newResourceData: {
        ports: [],
      },
    };
  },

  methods: {
    addPort(newPort) {
      const isNumeric = !!newPort.match(/^\d+$/);
      const isRange = !!newPort.match(/^(\d+):(\d+)$/);

      if (!isNumeric && !isRange) {
        notyError('Port should be numeric or range of ports (ex: 8000:8080)');
        return;
      }

      this.current.ports.push(newPort);
    },
  },
};
</script>
