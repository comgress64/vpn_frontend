import Vue from 'vue';

//
// Directive to make a column in table sortable
//

export default {
  params: ['name'],

  bind(el, bindings, vnode) {
    const initialHtml = el.innerHTML;
    const { def: self, value: field } = bindings;
    const vm = vnode.context;

    self.updateHtml(initialHtml, el, bindings, vnode);

    self.handler = (e) => {
      let order = vm.params.order;

      // Change order only if user clicked on same column
      if (vm.params.sort === field) {
        if (order === 'asc') {
          order = 'desc';
        }
        else {
          order = 'asc';
        }
      }

      Vue.set(vm.params, 'order', order);
      Vue.set(vm.params, 'sort', field);

      vm.getList();

      e.preventDefault();
    };

    // When data from server loaded, then redraw arrow
    vm.$on('rest-data-loaded', () => {
      self.updateHtml(initialHtml, el, bindings, vnode);
    });

    el.addEventListener('click', self.handler);
  },

  unbind(el, bindings) {
    const { def: self } = bindings;
    el.removeEventListener('click', self.handler);
  },

  updateHtml(initialHtml, el, bindings, vnode) {
    const { def: self, value: field } = bindings;
    const vm = vnode.context;

    const arrow = self.getArrow(field, vm.params.sort, vm.params.order);

    // Disable no-param-reassign rule, because where is no other way to avoid
    // param reassignment
    // eslint-disable-next-line no-param-reassign
    el.innerHTML = `
      <a href="#" class="pointer">
        ${initialHtml}
        <span>${arrow}<span>
      </a>
    `;
  },

  getArrow(field, sort, order) {
    let arrow = null;

    if (sort === field) {
      if (order === 'asc') {
        arrow = '↑';
      }
      else if (order === 'desc') {
        arrow = '↓';
      }
    }

    if (arrow == null) {
      arrow = '';
    }

    return arrow;
  },
};
