<template>
  <span>
    <small class="text-danger" v-for="error in errors">{{ error }}</small>
  </span>
</template>
<script>
//
// Component what displays error messages in forms
//

export default {
  props: ['errors'],
};
</script>
<style scoped>
.text-danger {
	color: red;
}
</style>
