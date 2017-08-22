<template>
  <div class="col-xs-6 col-xs-offset-3 col-md-4 col-md-offset-4">
    <!-- BEGIN login -->
    <div role="dialog">
      <div class="modal-dialog" style="width: auto;">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Login</h4>
          </div>
          <div class="modal-body">
            <form @keyup.13="Auth.login(email, password, redirectTo)">
              <div class="form-group">
                <label for="login_email">E-mail</label>
                <input type="text" class="form-control" v-model="email">
              </div>
              <div class="form-group">
                <label for="login_password">Password</label>
                <input type="password" class="form-control" v-model="password">
              </div>
              <div class="form-group">
                <error-messages :errors="Auth.errors.login"></error-messages>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <a href="#" class="pull-left">Forgot password?</a>
            <button type="button" class="btn btn-primary" @click="Auth.login(email, password, redirectTo)">
              Login
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- END login -->

  </div>
</template>
<script>
import ErrorMessages from '_shared/components/errorMessages';

export default {

  components: { ErrorMessages },

  data() {
    return {
      email: '',
      password: '',
      redirectTo: '/',
    };
  },
};
</script>
