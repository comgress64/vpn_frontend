<template>
<div class="new-header">
  <div class="new-subheader">
    <div class="active-app">VPN Management System</div>
    <div class="toggle-app">
      <button class="btn btn-dark"><i class="fa fa-bars"></i></button>
    </div>
  </div>
  <ul class="new-navigation">
    <a href="/" class="logo">
    <img src="/build/resources/assets/img/logo.png" alt="">
    </a>
    <li v-if="Auth.isAuthenticated" class="has-child profile" :class="{ opened: visibleItem === 'profile' }" @click="toggle('profile')">
      <a href="#.">
        <span v-if="!isUserEmpty">{{ Auth.user.fullname }}</span>
      </a>
      <ul class="child">
        <li>
          <a href="#" @click.prevent="$modals.profile.$show()">Profile</a>
        </li>
        <li>
          <a href="/auth/logout">Logout</a>
        </li>
      </ul>
    </li>
  </ul>
</div>
</template>
<script>
import $ from 'jquery';

export default {

  created() {
    $(window).on('click', (event) => {
      const $target = $(event.target);
      const clickedOnItem = $('.new-navigation').has($target).length > 0;
      if (!clickedOnItem) {
        this.visibleItem = null;
      }
    });
  },

  data() {
    return {
      visibleItem: null,
    };
  },

  computed: {
    isUserEmpty() {
      return this.Auth.user == null || this.Auth.user.id == null;
    },
  },

  methods: {
    toggle(name) {
      if (this.visibleItem === name) {
        this.visibleItem = null;
      }
      else if (this.visibleItem == null || this.visibleItem !== name) {
        this.visibleItem = name;
      }
    },
  },

};
</script>
