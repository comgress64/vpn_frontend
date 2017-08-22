export default {
  props: {
    currentPage: {
      type: Number,
      default: 1,
    },

    pageSize: {
      type: Number,
      default: 25,
    },

    totalItems: {
      type: Number,
      default: 0,
    },
  },

  data() {
    return {
      pages: [],
      totalPages: 0,
      internalPage: this.currentPage,
    };
  },

  methods: {
    onPageSelected(page) {
      this.$emit('page-changed', page);
    },

    setPage(page) {
      if (page <= 0 || page > this.totalPages) return;

      this.internalPage = page;
      this.updatePages();
      this.onPageSelected(page);
    },

    prevPage() {
      if (this.internalPage === 1) {
        return;
      }
      this.setPage(this.internalPage - 1);
    },

    nextPage() {
      if (this.internalPage === this.totalPages) {
        return;
      }
      this.setPage(this.internalPage + 1);
    },

    // Update starting and ending pages
    updatePages() {
      let begin = this.internalPage - 2;

      if (this.internalPage - 2 <= 1) {
        begin = 1;
      }
      if (this.internalPage === this.totalPages) {
        begin -= 2;
      }
      else if (this.internalPage === this.totalPages - 1) {
        begin -= 1;
      }
      if (begin < 1) {
        begin = 1;
      }

      let end = this.internalPage + 2;

      if (this.internalPage + 2 > this.totalPages) {
        end = this.totalPages;
      }
      if (this.internalPage === 1) {
        end += 2;
      }
      else if (this.internalPage === 2) {
        end += 1;
      }
      if (end > this.totalPages) {
        end = this.totalPages;
      }
      if (!end) {
        end = begin;
      }
      this.pages = Number.range(begin, end).toArray();
    },

    updateTotalPages() {
      this.totalPages = (this.totalItems / this.pageSize).floor();
      if (this.totalItems % this.pageSize > 0 || this.totalItems === 0) {
        this.totalPages += 1;
      }
    },
  },

  watch: {
    totalItems() {
      this.updateTotalPages();
      this.updatePages();
    },

    currentPage(value) {
      this.setPage(value);
      this.updateTotalPages();
      this.updatePages();
    },

    pageSize() {
      this.updateTotalPages();
      this.updatePages();
    },
  },
};
