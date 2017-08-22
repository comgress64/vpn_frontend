export default {

  data() {
    return {
      filterOpened: false,
    };
  },

  methods: {
    toggleFilter() {
      this.filterOpened = !this.filterOpened;
    },
  },
};
