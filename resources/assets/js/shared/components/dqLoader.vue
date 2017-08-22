<template>
  <div class="loader">
    <spinner v-if="internalLoading" class="spinner"></spinner>
    <slot v-else></slot>
  </div>
</template>
<script>
import Spinner from './circleSpinner';

export default {

  props: ['loading'],

  components: { Spinner },

  data() {
    return {
      internalLoading: (Object.isBoolean(this.loading) ? this.loading : false),
    };
  },

  watch: {
    loading(newVal) {
      if (Object.isBoolean(newVal)) {
        this.internalLoading = newVal;
      }
      else if (newVal.then != null) {
        this.internalLoading = true;
        newVal.finally(() => {
          this.internalLoading = false;
        });
      }
      else {
        this.internalLoading = false;
      }
    },
  },

};
</script>
<style lang="sass" scoped>
  .loader {
    width: 100%;
    height: 100%;
    margin: auto;
  }

  .spinner {
  }
</style>
