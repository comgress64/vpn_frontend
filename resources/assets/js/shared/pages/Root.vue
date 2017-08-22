<template>
  <body>
    <section id="header">
      <vpn-header></vpn-header>
    </section>
    <section id="new-main">
      <div id="content">
        <main>

          <div class="container-fluid">
            <div class="row">
              <!-- BEGIN side navigation -->
              <div class="col-xs-12 col-md-2 sidenav" v-if="showMenu">
                <p v-if="Auth.isSuperadmin || Auth.hasPermission('manage_groups')">
                  <router-link to="groups" class="btn btn-primary btn-lg btn-icon btn-block">
                    <i class="fa fa-building-o"></i> Groups
                  </router-link>
                </p>
                <p v-if="!Auth.isUser">
                  <router-link to="users" class="btn btn-primary btn-lg btn-icon btn-block">
                    <i class="fa fa-users"></i> Users
                  </router-link>
                </p>
                <p v-if="Auth.isSuperadmin || Auth.hasPermission('manage_devices')">
                  <router-link to="devices" class="btn btn-primary btn-lg btn-icon btn-block">
                    <i class="fa fa-desktop"></i> Devices
                  </router-link>
                </p>
              </div>
              <!-- END side navigation -->

              <router-view></router-view>
            </div>
          </div>
        </main>
      </div>
    </section>
    <section id="footer">
      <footer class="vertical-align">
        <div class="container-fluid">
          <div class="row">
            <div class="col-xs-8 col-xs-offset-2 text-center">
              © 2017 Copyright. Not for distribution to third parties.
            </div>
          </div>
        </div>
      </footer>
    </section>

    <profile-modal></profile-modal>
  </body>
</template>
<script>
import Vue from 'vue';
import '_assets/img/logo.png';
import VpnHeader from '_components/header';
import ProfileModal from '_components/profileModal';

export default {

  components: { VpnHeader, ProfileModal },

  mounted() {
    this.gitRevision = Vue.gitRev;
    this.version = Vue.appVersion;
    this.env = Vue.env;
    this.projectName = Vue.projectName;
  },

  data() {
    return {
      gitRevision: null,
      version: null,
      env: null,
      projectName: null,
    };
  },

  computed: {
    showMenu() {
      return (this.$route.meta.showMenu != null && !this.$route.meta.showMenu) ||
        this.$route.meta.showMenu == null;
    },
  },
};
</script>
<style>
  .sidenav {
    text-align: center;
  }

  .sidenav button {
    width: 100%; max-width: 200px;
  }
</style>
