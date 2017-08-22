import objectAssign from 'object-assign';
import Sugar from '_shared/lib/mySugar';
import catcherNotifier from '_shared/services/catcherNotifier';

export default function (resource) {
  return {
    created() {
      this.clearFilter();

      // Update params with default params
      this.params = objectAssign({}, this.params, this.defaultParams);

      if (this.getListOnLoad) {
        this.getList();
      }

      // Load item if its id is passed in query params
      const itemId = this.$route.query.itemId;
      if (itemId && this.getItemOnLoad) {
        const loadItem = () => {
          this.getItem(itemId).then(() => {
            if (this.showModalOnItemLoad) {
              this.showModal(this.showModalOnItemLoad);
            }
            this.$emit('item-loaded', itemId);
          });
        };

        // If list is also loaded, then load item after list loading is finished
        if (this.getListOnLoad) {
          this.$once('rest-data-loaded', loadItem);
        }
        else {
          loadItem();
        }
      }
    },

    data() {
      return {
        // Current used resource
        resource,

        // Active resource instance (used in form)
        current: {},

        // Which variable to use to store new resource data
        newResource: 'current',

        // Active resource original instance (used in form)
        originalCurrent: {},

        // List of errors for current resource instance
        errors: {},

        // Default data for new resource instance
        newResourceData: {},

        // List of items coming from query
        data: [],

        // Additional data from query
        meta: {},

        // Query params
        params: {},

        defaultParams: {
          sort: 'id',
          order: 'desc',
        },

        // Filter params
        filter: {},

        // See method substituteKeys
        selectKeys: {},

        // Default params for filter
        filterDefaults: {},

        // Pager params
        currentPage: 1,
        itemsPerPage: 50,

        // Indicates that data has initially loaded
        dataLoaded: false,

        // Should list be reloaded when page loads
        getListOnLoad: true,

        // Should item be loaded when page loads
        // if itemId is passed in query params
        getItemOnLoad: true,

        // Should modal be opened when single item is loaded
        // if itemId is passed
        showModalOnItemLoad: true,

        // Should list be reloaded when new resource saved
        getListOnCreate: true,

        // Should list be reloaded when resource updated
        getListOnUpdate: true,

        // Should fresh resource be initialized when new resource was created
        makeNewAfterCreate: true,

        // Should fresh resource be initialized when list loads after saving
        makeNewAfterListLoad: false,

        // Flag determining that current record is saving
        saving: false,

        // Flag determining that current record is destroying
        destroying: false,

        // Flag determining that list is loading
        loading: false,

        // Flag determining that item is loading
        itemLoading: false,
      };
    },

    computed: {
      isDataEmpty() {
        return (Object.isObject(this.data) && Object.isEmpty(this.data)) ||
          this.data.length === 0;
      },
    },

    methods: {

      // Mostly internal method used to collect query params for list method
      // from different sources like general params, filter, pagination, etc.
      getParams(params = {}) {
        // Exclude all keys from filter that are equal to default filter value
        const filter = Object.filter(this.filter, (value, key) =>
           !Object.isEqual(value, this.filterDefaults[key])
        );
        return objectAssign({}, this.params, filter, {
          page: this.currentPage,
          itemsPerPage: this.itemsPerPage,
        }, params);
      },

      // Override this method if you want more control of what params are passed
      // to list query
      beforeQuery(params) {
        return params;
      },

      // When filtering we do not want to send whole object to server instead of an id
      // But vue-multiselect operates whole objects, so selectFilterKey defines a
      // correspondance of filter name and which key should be taken from it
      // E.g. location => 'id' would say, that filter.location would be changed
      // to filter.location.id
      substituteKeys(params) {
        return Object.reduce(params, (obj, value, key) => {
          const appendKey = this.selectKeys[key];
          const merge = {};
          if (appendKey == null) {
            merge[key] = value;
          }
          else {
            let val;
            let newKey;
            if (Array.isArray(value)) {
              val = value.map(item => item[appendKey]);
              newKey = key.singularize() + appendKey.pluralize().capitalize();
            }
            else {
              val = Object.has(value, appendKey) ? value[appendKey] : null;
              newKey = key + appendKey.capitalize();
            }
            merge[newKey] = val;
          }
          return objectAssign({}, obj, merge);
        }, {});
      },

      // Override this method if you want to customize what is done on page change
      onPageChanged(page) {
        this.currentPage = page;
        this.getList();
      },

      // Override this method if you want to customize what is done
      // when user changes number of displayed items per page
      onItemsPerPageChanged() {
        this.currentPage = 1;
        this.getList();
      },

      // Method to pass to update event of vue-multiselect in filter
      // to prevent too many same looking methods
      onFilterSelected(value, id) {
        this.$set(`filter.${id}`, value);
      },

      // Method to pass to update event of vue-multiselect in current
      // to prevent too many same looking methods
      onCurrentSelected(value, id) {
        this.$set(`current.${id}`, value);
      },

      // Override this method if you want to do something with each item in list
      // after it is loaded
      changeItemAfterLoad(item) {
        return item;
      },

      // Get one item by id
      getItem(id) {
        const queryParams = { id };
        const params = this.getParams();
        if (params.with != null) {
          queryParams.with = params.with;
        }
        this.itemLoading = true;
        return this.resource.get(queryParams).then((response) => {
          if (response.body.data != null) {
            this.current = response.body.data;
          }
          else {
            this.current = response.body;
          }
          this.changeItemAfterLoad(this.current);
        }).catch(catcherNotifier).finally(() => {
          this.itemLoading = false;
        });
      },

      // Load list of items
      // Basically should not be overriden
      // Emits rest-data-loaded event
      getList(params = {}) {
        this.loading = true;

        let queryParams = this.getParams(params);
        queryParams = this.substituteKeys(queryParams);
        queryParams = this.beforeQuery(queryParams);
        return this.resource.query(queryParams).then((response) => {
          this.data = response.body.data;

          // Copy response meta to this.meta, in case of conflict response wins
          this.meta = objectAssign({}, this.meta, response.body.meta);

          if (Object.isObject(this.data)) {
            this.data = new Sugar.Object(this.data);
          }
          this.data.forEach(this.changeItemAfterLoad.bind(this));
          if (this.data.raw) {
            this.data = this.data.raw;
          }

          // emit AND broadcast same event
          // emit needed to catch it in the same scope (in sortable-column directive
          // particularly)
          // broadcast needed to catch it in other scope (in other component)
          // TODO: explanation or workaround needed
          this.$emit('rest-data-loaded', queryParams);
          // this.$broadcast('rest-data-loaded', queryParams);

          this.dataLoaded = true;
        }).catch(catcherNotifier).finally(() => {
          this.loading = false;
        });
      },

      // Set filter to its default state
      clearFilter() {
        this.$set(this, 'filter', JSON.parse(JSON.stringify(this.filterDefaults)));
        this.$emit('clear-filter');
        // this.$broadcast('clear-filter');
      },

      // Prepare form for creating new resource
      newItem(openModal = true) {
        this.makeNew();
        this.errors = {};

        // if use @click="newItem"
        if (openModal.constructor.name === 'MouseEvent') {
          // eslint-disable-next-line no-param-reassign
          openModal = true;
        }
        if (Object.isBoolean(openModal) && openModal) {
          this.showModal();
        }
        else if (Object.isString(openModal)) {
          this.showModal(openModal);
        }
        this.$emit('new');
      },

      // Prepare form for editing a resource
      edit(item, openModal = true) {
        this.current = JSON.parse(JSON.stringify(item));
        this.originalCurrent = JSON.parse(JSON.stringify(item));
        this.errors = {};
        if (Object.isBoolean(openModal) && openModal) {
          this.showModal();
        }
        else if (Object.isString(openModal)) {
          this.showModal(openModal);
        }
        this.$emit('editing');
      },

      // Restore original item
      restoreOriginal() {
        if (this.originalCurrent.id != null) {
          this.current = objectAssign({}, this.originalCurrent);
        }
        else {
          this.makeNew();
        }
        this.$emit('original-restored');
      },

      // Override this method if you want to refer modal window with another name
      // Modal should be instance of ui-modal
      getModal(name = null) {
        let modalName = name;
        if (modalName == null || Object.isBoolean(modalName)) {
          modalName = `${this.resource.name}FormModal`;
        }
        const modal = this.$modals[modalName];

        if (modal == null) {
          modalName = `${this.resource.name}Form`;
        }
        return this.$modals[modalName];
      },

      showModal(name = null) {
        if (this.getModal(name)) {
          setTimeout(() => {
            this.getModal(name).$show();
          }, 0);
        }
      },

      hideModal(name = null) {
        if (this.getModal(name)) {
          this.getModal(name).$hide();
        }
      },

      // Override this method if you want to do something before item is destroyed
      beforeDestroy() {
      },

      destroy(item, closeModal = true) {
        if (this.destroying) {
          return false;
        }

        this.destroying = true;

        let itemToDestroy = null;

        if (Number.isInteger(item)) {
          itemToDestroy = { id: item };
        }
        else {
          itemToDestroy = item;
        }

        this.$emit('destroying', itemToDestroy);

        this.beforeDestroy(itemToDestroy);

        return this.resource.remove(itemToDestroy).then(() => {
          this.$emit('destroyed');
          if (closeModal) {
            this.hideModal();
          }
          this.getList();
        }).catch(catcherNotifier).finally(() => {
          this.destroying = false;
        });
      },

      // Override this method if you want to do something before item is saved
      beforeSave() {
      },

      // Save record
      // If no item is passed, current record will be saved
      save(item, closeModal = true) {
        if (this.saving) {
          return false;
        }

        this.saving = true;

        let itemToSave = null;
        if (item != null) {
          itemToSave = item;
        }
        else {
          itemToSave = this.current;
        }

        itemToSave = this.substituteKeys(itemToSave);

        this.errors = {};

        this.beforeSave(itemToSave);

        let promise;
        if (itemToSave.id != null) {
          promise = this.resource.update({ id: itemToSave.id }, itemToSave);
        }
        else {
          promise = this.resource.save({ id: null }, itemToSave);
        }

        return promise.then((response) => {
          const canLoadList =
            (itemToSave.id == null && this.getListOnCreate) ||
            (itemToSave.id != null && this.getListOnUpdate);

          const canInitNew =
            itemToSave.id == null &&
            itemToSave === this.current &&
            this.makeNewAfterCreate;

          let updatedCurrent = response.body;

          if (updatedCurrent.data != null) {
            updatedCurrent = updatedCurrent.data;
          }

          if (canInitNew) {
            this.new();
          }

          if (canLoadList) {
            this.getList().then(() => {
              if (this.removeCurrentAfterListLoad) {
                this.current = null;
              }
            });
          }

          if (closeModal) {
            this.hideModal(closeModal);
          }

          if (itemToSave.id) {
            this.$emit('updated', itemToSave.id);
          }
          else {
            itemToSave.id = updatedCurrent.id;
            this.$emit('created', updatedCurrent.id);
          }

          return itemToSave;
        }).catch((response) => {
          this.errors = response.body;
          this.$emit('save-error', this.errors);
          catcherNotifier(response);
          throw response;
        }).finally(() => {
          // Close modal after 250 ms so modal has time to hide
          // fix for flash (super hero) users
          setTimeout(() => {
            this.saving = false;
          }, 250);
        });
      },

      // Get errors by path to them
      // Path should exclude 'errors'
      // This is needed if there is no such path, because
      // vue says it is undefined
      getErrors(path) {
        return Object.get(this.errors, path);
      },

      makeNew() {
        this[this.newResource] = JSON.parse(JSON.stringify(this.newResourceData));
      },
    },
  };
}
