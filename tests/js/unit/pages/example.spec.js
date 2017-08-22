import 'spec_helper';
import Vue from 'vue';
import ExamplePage from '_admin/pages/Example';
import router from './../router';

describe('Example page', () => {
  let vm;

  beforeEach(() => {
    const root = new Vue({
      router,
      render(h) {
        return h(ExamplePage);
      },
    }).$mount();
    vm = root.$children.first();
  });

  it('has default message', () => {
    expect(vm.message).toEqual('Hello world');
  });
});
