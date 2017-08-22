<template>
  <body>
    <section id="header">
    </section>
    <section id="new-main">
      <div id="content">
        <main>

          <div class="container-fluid">
            <div class="row">
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
  </body>
</template>
<script>
import Vue from 'vue';
import '_assets/img/logo.png';
import VpnHeader from '_components/header';

export default {

  components: { VpnHeader },

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
