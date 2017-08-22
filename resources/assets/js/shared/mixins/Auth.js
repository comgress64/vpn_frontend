import Vue from 'vue';
import Auth from '_shared/services/Auth';

export default Vue.mixin({
  data() {
    return { Auth };
  },
});
