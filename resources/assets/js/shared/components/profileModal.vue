<template>
  <vudal name="profile">
    <div class="header">
      <button type="button" class="close">&times;</button>
      <h4 class="modal-title">Profile</h4>
    </div>
    <div class="modal-body contents">
      <div class="form-group">
        <label for="profile_firstName">First Name</label>
        <input type="text" class="form-control" v-model="current.fname">
        <error-messages :errors="errors.fname"></error-messages>
      </div>
      <div class="form-group">
        <label for="profile_secondName">Last Name</label>
        <input type="text" class="form-control" v-model="current.lname">
        <error-messages :errors="errors.lname"></error-messages>
      </div>
      <div class="form-group">
        <label for="profile_email">E-mail</label>
        <input type="text" class="form-control" v-model="current.email">
        <error-messages :errors="errors.email"></error-messages>
      </div>
      <div class="form-group">
        <label for="profile_password">Password</label>
        <input type="password" class="form-control" v-model="current.password">
        <error-messages :errors="errors.password"></error-messages>
      </div>
      <div class="form-group">
        <label for="profile_confirmPassword">Confirm Password</label>
        <input type="password" class="form-control" v-model="current.passwordConfirmation">
        <error-messages :errors="errors.passwordConfirmation"></error-messages>
      </div>
      <div class="form-group">
        <label for="profile_phone">Phone</label>
        <input type="text" class="form-control" v-model="current.phone">
        <error-messages :errors="errors.phone"></error-messages>
      </div>
      <div class="form-group">
        <label for="profile_mobile">Mobile</label>
        <input type="text" class="form-control" v-model="current.mobile">
        <error-messages :errors="errors.mobile"></error-messages>
      </div>
      <div class="form-group">
        <p>
          Get VPN Key: <a href="#" @click.prevent="downloadKey()">download</a>
        </p>
      </div>
    </div>
    <div class="actions">
      <button type="button" class="btn btn-default cancel">Close</button>
      <button type="button" class="btn btn-primary" @click="save()">Save</button>
    </div>
  </vudal>
</template>
<script>
import objectAssign from 'object-assign';
import Vudal from 'vudal';
import Account from '_shared/services/Account';
import { notySuccess } from '_shared/services/Notifications';
import ErrorMessages from '_components/errorMessages';

export default {

  components: { Vudal, ErrorMessages },

  created() {
    this.current = objectAssign({}, this.Auth.user);
  },

  data() {
    return {
      current: {},
      errors: {},
    };
  },

  methods: {
    save() {
      Account.update(this.current).then(() => {
        notySuccess('Profile successfully updated');
        this.errors = {};
        this.$modals.profile.$hide();
      }).catch((response) => {
        this.errors = response.body;
      });
    },

    downloadKey() {
      location.href = `/users/${this.current.id}/download_key`;
    },
  },

  watch: {
    'Auth.user': function () {
      this.current = objectAssign({}, this.Auth.user);
    },
  },

};
</script>
