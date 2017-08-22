<template>
<ul class="pagination">
  <li class="paginate_button first" :class="{disabled: internalPage == 1}">
    <a href="#" tabindex="0" @click.prevent="setPage(1)">First</a>
  </li>
  <li class="paginate_button previous" :class="{disabled: internalPage - 1 == 0}">
    <a href="#" tabindex="0" @click.prevent="setPage(internalPage - 1)">Previous</a>
  </li>
  <li class="paginate_button" :class="{active: page == internalPage}" v-for="page in pages" @click.prevent="setPage(page)">
    <a href="#" tabindex="0">{{ page }}</a>
  </li>
  <li class="paginate_button next" :class="{disabled: internalPage + 1 == pages.length + 1}">
    <a href="#" tabindex="0" @click.prevent="setPage(internalPage + 1)">Next</a>
  </li>
  <li class="paginate_button last" :class="{disabled: internalPage == pages.length}">
    <a href="#" tabindex="0" @click.prevent="setPage(pages.length)">Last</a>
  </li>
</ul>
</template>
<script>
//
// Component for drawing a pager
// Throws an event "page-changed" with new page as an argument
//

import pager from '_shared/mixins/pager';

export default {
  mixins: [pager],
};
</script>
