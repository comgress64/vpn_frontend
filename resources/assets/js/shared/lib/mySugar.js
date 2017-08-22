require('sugar/string');
require('sugar/number');
require('sugar/array');
require('sugar/enumerable');
require('sugar/object');
require('sugar/range');

require('sugar-inflections/string/pluralize');
require('sugar-inflections/string/singularize');
require('sugar-inflections/string/humanize');

const Sugar = require('sugar-core');

Sugar.extend();

Array.prototype.find = function(search) {
  if (this && this.length && Array.isArray(this)) {
    return this.filter(search).first();
  }

  return null;
}

module.exports = Sugar;
