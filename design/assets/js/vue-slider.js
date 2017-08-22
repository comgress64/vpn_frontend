Vue.component('vue-slider', {
	data: function data() {
		return {
			flag: false,
			size: 0,
			currentValue: 0,
			currentSlider: 0
		};
	},

	template: '\
	<div v-el:wrap :class="[\'vue-slider-wrap\', flowDirection, disabledClass]" v-show="show" :style="wrapStyles" @click="wrapClick">\
		<div v-el:elem class="vue-slider" :style="elemStyles">\
			<template v-if="isMoblie">\
				<template v-if="isRange">\
					<div v-el:dot0 :data-slierValue="val[0]" :class="[ tooltipStatus, \'vue-slider-tooltip-\' + tooltipDirection, \'vue-slider-dot\']" :style="dotStyles" @touchstart="moveStart(0)"></div>\
					<div v-el:dot1 :data-slierValue="val[1]" :class="[ tooltipStatus, \'vue-slider-tooltip-\' + tooltipDirection, \'vue-slider-dot\']" :style="dotStyles" @touchstart="moveStart(1)"></div>\
				</template>\
				<template v-else>\
					<div v-el:dot :data-slierValue="val" :class="[ tooltipStatus, \'vue-slider-tooltip-\' + tooltipDirection, \'vue-slider-dot\']" :style="dotStyles" @touchstart="moveStart"></div>\
				</template>\
			</template>\
			<template v-else>\
				<template v-if="isRange">\
					<div v-el:dot0 :data-slierValue="val[0]" :class="[ tooltipStatus, \'vue-slider-tooltip-\' + tooltipDirection, \'vue-slider-dot\']" :style="dotStyles" @mousedown="moveStart(0)"></div>\
					<div v-el:dot1 :data-slierValue="val[1]" :class="[ tooltipStatus, \'vue-slider-tooltip-\' + tooltipDirection, \'vue-slider-dot\']" :style="dotStyles" @mousedown="moveStart(1)"></div>\
				</template>\
				<template v-else>\
					<div v-el:dot :data-slierValue="val" :class="[ tooltipStatus, \'vue-slider-tooltip-\' + tooltipDirection, \'vue-slider-dot\']" :style="dotStyles" @mousedown="moveStart"></div>\
				</template>\
			</template>\
			<template v-if="piecewise">\
				<ul class="vue-slider-piecewise">\
					<li v-for="position in piecewiseDotPos" :style="[piecewiseStyle, position]"></li>\
				</ul>\
			</template>\
			<div v-el:process class="vue-slider-process"></div>\
		</div>\
	</div>\
	',
	props: {
		width: {
			type: [Number, String],
			default: 'auto'
		},
		height: {
			type: [Number, String],
			default: 4
		},
		data: {
			type: Array,
			default: null
		},
		dotSize: {
			type: Number,
			default: 16
		},
		min: {
			type: Number,
			default: 0
		},
		max: {
			type: Number,
			default: 100
		},
		interval: {
			type: Number,
			default: 1
		},
		show: {
			type: Boolean,
			default: true
		},
		disabled: {
			type: Boolean,
			default: false
		},
		piecewise: {
			type: Boolean,
			default: false
		},
		tooltip: {
			type: [String, Boolean],
			default: 'always'
		},
		eventType: {
			type: String,
			default: 'auto'
		},
		direction: {
			type: String,
			default: 'horizontal'
		},
		tooltipDir: {
			type: String
		},
		reverse: {
			type: Boolean,
			default: false
		},
		speed: {
			type: Number,
			default: 0.5
		},
		value: {
			type: [String, Number, Array],
			default: 0
		}
	},
	computed: {
		flowDirection: function flowDirection() {
			return 'vue-slider-' + (this.direction + (this.reverse ? '-reverse' : ''));
		},
		tooltipDirection: function tooltipDirection() {
			return this.tooltipDir || (this.direction === 'vertical' ? 'left' : 'top');
		},
		tooltipStatus: function tooltipStatus() {
			if (this.tooltip === 'hover' && this.flag) return 'vue-slider-always';
			return this.tooltip ? 'vue-slider-' + this.tooltip : '';
		},
		isMoblie: function isMoblie() {
			if (this.eventType === 'touch') {
				return true;
			} else if (this.eventType === 'mouse') {
				return false;
			} else {
				return (/(iPhone|iPad|iPod|iOS|Android|SymbianOS|Windows Phone|Mobile)/i.test(navigator.userAgent)
				);
			}
		},
		isDisabled: function isDisabled() {
			return this.eventType === 'none' ? true : this.disabled;
		},
		disabledClass: function disabledClass() {
			return this.disabled ? 'vue-slider-disabled' : '';
		},
		isRange: function isRange() {
			return Array.isArray(this.value);
		},
		slider: function slider() {
			if (this.isRange) {
				return [this.$els.dot0, this.$els.dot1];
			} else {
				return this.$els.dot;
			}
		},
		minimum: function minimum() {
			if (this.data) {
				return 0;
			}
			return this.min;
		},
		val: {
			get: function get() {
				if (this.data) {
					if (this.isRange) {
						return [this.data[this.currentValue[0]], this.data[this.currentValue[1]]];
					}
					return this.data[this.currentValue];
				}
				return this.currentValue;
			},
			set: function set(val) {
				if (this.data) {
					if (this.isRange) {
						var index0 = this.data.indexOf(val[0]);
						var index1 = this.data.indexOf(val[1]);
						if (index0 > -1 && index1 > -1) {
							this.currentValue = [index0, index1];
						}
					} else {
						var index = this.data.indexOf(val);
						if (index > -1) {
							this.currentValue = index;
						}
					}
				} else {
					this.currentValue = val;
				}
			}
		},
		maximum: function maximum() {
			if (this.data) {
				return this.data.length - 1;
			}
			return this.max;
		},
		spacing: function spacing() {
			if (this.data) {
				return 1;
			}
			return this.interval;
		},
		total: function total() {
			if (this.data) {
				return this.data.length - 1;
			}
			if ((this.maximum - this.minimum) % this.interval !== 0) {
				console.error('[Vue warn]: Prop[interval] is illegal, Please make sure that the interval can be divisible');
			}
			return (this.maximum - this.minimum) / this.interval;
		},
		gap: function gap() {
			return this.size / this.total;
		},
		offset: function offset() {
			return this.direction === 'vertical' ? this.$els.elem.getBoundingClientRect().top + window.pageYOffset || document.documentElement.scrollTop : this.$els.elem.getBoundingClientRect().left;
		},
		position: function position() {
			if (this.isRange) {
				return [(this.currentValue[0] - this.minimum) / this.spacing * this.gap, (this.currentValue[1] - this.minimum) / this.spacing * this.gap];
			}
			return (this.currentValue - this.minimum) / this.spacing * this.gap;
		},
		limit: function limit() {
			if (this.isRange) {
				return [[0, this.position[1]], [this.position[0], this.size]];
			}
			return [0, this.size];
		},
		valueLimit: function valueLimit() {
			if (this.isRange) {
				return [[this.minimum, this.currentValue[1]], [this.currentValue[0], this.maximum]];
			}
			return [this.minimum, this.maximum];
		},
		wrapStyles: function wrapStyles() {
			if (this.direction === 'vertical') {
				return {
					height: typeof this.height === 'number' ? this.height + 'px' : this.height,
					padding: this.dotSize / 2 + 'px'
				};
			}
			return {
				width: typeof this.width === 'number' ? this.width + 'px' : this.width,
				padding: this.dotSize / 2 + 'px'
			};
		},
		elemStyles: function elemStyles() {
			if (this.direction === 'vertical') {
				return {
					width: this.width + 'px',
					height: '100%'
				};
			}
			return {
				height: this.height + 'px'
			};
		},
		dotStyles: function dotStyles() {
			if (this.direction === 'vertical') {
				return {
					width: this.dotSize + 'px',
					height: this.dotSize + 'px',
					left: -(this.dotSize - this.width) / 2 + 'px'
				};
			}
			return {
				width: this.dotSize + 'px',
				height: this.dotSize + 'px',
				top: -(this.dotSize - this.height) / 2 + 'px'
			};
		},
		piecewiseStyle: function piecewiseStyle() {
			if (this.direction === 'vertical') {
				return {
					width: this.width + 'px',
					height: this.width + 'px'
				};
			}
			return {
				width: this.height + 'px',
				height: this.height + 'px'
			};
		},
		piecewiseDotPos: function piecewiseDotPos() {
			var arr = [];
			for (var i = 1; i < this.total; i++) {
				arr.push(this.direction === 'vertical' ? {
					bottom: this.gap * i - this.width / 2 + 'px',
					left: '0px'
				} : {
					left: this.gap * i - this.height / 2 + 'px',
					top: '0px'
				});
			}
			return arr;
		}
	},
	watch: {
		currentValue: function currentValue(val) {
			this.value = this.val;
		},
		value: function value(val) {
			this.flag || this.setValue(val);
		},
		show: function show(bool) {
			var _this = this;

			if (bool && !this.size) {
				this.$nextTick(function () {
					_this.refresh();
				});
			}
		}
	},
	methods: {
		bindEvents: function bindEvents() {
			if (this.isMoblie) {
				document.addEventListener('touchmove', this.moving);
				document.addEventListener('touchend', this.moveEnd);
			} else {
				document.addEventListener('mousemove', this.moving);
				document.addEventListener('mouseup', this.moveEnd);
				document.addEventListener('mouseleave', this.moveEnd);
			}
		},
		unbindEvents: function unbindEvents() {
			window.removeEventListener('resize', this.refresh);

			if (this.isMoblie) {
				document.removeEventListener('touchmove', this.moving);
				document.removeEventListener('touchend', this.moveEnd);
			} else {
				document.removeEventListener('mousemove', this.moving);
				document.removeEventListener('mouseup', this.moveEnd);
				document.removeEventListener('mouseleave', this.moveEnd);
			}
		},
		getPos: function getPos(e) {
			var pos = void 0;
			if (this.direction === 'vertical') {
				pos = this.reverse ? e.pageY - this.offset : this.size - (e.pageY - this.offset);
			} else {
				pos = this.reverse ? this.size - (e.clientX - this.offset) : e.clientX - this.offset;
			}
			return pos;
		},
		wrapClick: function wrapClick(e) {
			if (this.isDisabled) return false;
			var pos = this.getPos(e);
			if (this.isRange) {
				this.currentSlider = pos > (this.position[1] - this.position[0]) / 2 + this.position[0] ? 1 : 0;
			}
			this.setValueOnPos(pos);
		},
		moveStart: function moveStart(index) {
			if (this.isDisabled) return false;else if (this.isRange) {
				this.currentSlider = index;
			}
			this.flag = true;
			this.$emit('drag-start', this);
		},
		moving: function moving(e) {
			if (!this.flag) return false;
			e.preventDefault();

			if (this.isMoblie) e = e.targetTouches[0];
			this.setValueOnPos(this.getPos(e), true);
		},
		moveEnd: function moveEnd(e) {
			if (this.flag) {
				this.$emit('drag-end', this);
			} else {
				return false;
			}
			this.flag = false;
			this.setPosition(this.speed);
		},
		setValueOnPos: function setValueOnPos(pos, bool) {
			var range = this.isRange ? this.limit[this.currentSlider] : this.limit;
			var valueRange = this.isRange ? this.valueLimit[this.currentSlider] : this.valueLimit;
			if (pos >= range[0] && pos <= range[1]) {
				this.setTransform(pos);
				var v = Math.round(pos / this.gap) * this.spacing + this.minimum;
				this.setCurrentValue(v, bool);
			} else if (pos < range[0]) {
				this.setTransform(range[0]);
				this.setCurrentValue(valueRange[0]);
				if (this.currentSlider === 1) this.currentSlider = 0;
			} else {
				this.setTransform(range[1]);
				this.setCurrentValue(valueRange[1]);
				if (this.currentSlider === 0) this.currentSlider = 1;
			}
		},
		isDiff: function isDiff(a, b) {
			if (Object.prototype.toString.call(a) !== Object.prototype.toString.call(b)) {
				return true;
			} else if (Array.isArray(a) && a.length === b.length) {
				return a.some(function (v, i) {
					return v !== b[i];
				});
			}
			return a !== b;
		},
		setCurrentValue: function setCurrentValue(val, bool) {
			if (val < this.minimum || val > this.maximum) return false;
			if (this.isRange) {
				if (this.isDiff(this.currentValue[this.currentSlider], val)) {
					this.currentValue.splice(this.currentSlider, 1, val);
					this.$emit('callback', this.val);
				}
			} else if (this.isDiff(this.currentValue, val)) {
				this.currentValue = val;
				this.$emit('callback', this.val);
			}
			bool || this.setPosition();
		},
		setIndex: function setIndex(val) {
			if (Array.isArray(val) && this.isRange) {
				var value = void 0;
				if (this.data) {
					value = [this.data[val[0]], this.data[val[1]]];
				} else {
					value = [this.spacing * val[0] + this.minimum, this.spacing * val[1] + this.minimum];
				}
				this.setValue(value);
			} else {
				val = this.spacing * val + this.minimum;
				if (this.isRange) {
					this.currentSlider = val > (this.currentValue[1] - this.currentValue[0]) / 2 + this.currentValue[0] ? 1 : 0;
				}
				this.setCurrentValue(val);
			}
		},
		setValue: function setValue(val) {
			if (this.isDiff(this.val, val)) {
				this.val = val;
				this.$emit('callback', this.val);
			}
			this.setPosition();
		},
		setPosition: function setPosition() {
			this.flag || this.setTransitionTime(this.speed);
			if (this.isRange) {
				this.currentSlider = 0;
				this.setTransform(this.position[this.currentSlider]);
				this.currentSlider = 1;
				this.setTransform(this.position[this.currentSlider]);
			} else {
				this.setTransform(this.position);
			}
			this.flag || this.setTransitionTime(0);
		},
		setTransform: function setTransform(val) {
			var value = (this.direction === 'vertical' ? this.dotSize / 2 - val : val - this.dotSize / 2) * (this.reverse ? -1 : 1);
			var translateValue = this.direction === 'vertical' ? 'translateY( ' + value + 'px )' : 'translateX( ' + value + 'px )';
			var processSize = (this.currentSlider === 0 ? this.position[1] - val : val - this.position[0]) + 'px';
			var processPos = (this.currentSlider === 0 ? val : this.position[0]) + 'px';
			if (this.isRange) {
				this.slider[this.currentSlider].style.transform = translateValue;
				this.slider[this.currentSlider].style.WebkitTransform = translateValue;
				this.slider[this.currentSlider].style.msTransform = translateValue;
				if (this.direction === 'vertical') {
					this.$els.process.style.height = processSize;
					this.$els.process.style[this.reverse ? 'top' : 'bottom'] = processPos;
				} else {
					this.$els.process.style.width = processSize;
					this.$els.process.style[this.reverse ? 'right' : 'left'] = processPos;
				}
			} else {
				this.slider.style.transform = translateValue;
				this.slider.style.WebkitTransform = translateValue;
				this.slider.style.msTransform = translateValue;
				if (this.direction === 'vertical') {
					this.$els.process.style.height = val + 'px';
				} else {
					this.$els.process.style.width = val + 'px';
				}
			}
		},
		setTransitionTime: function setTransitionTime(time) {
			time || this.$els.process.offsetWidth;
			if (this.isRange) {
				for (var i = 0; i < this.slider.length; i++) {
					this.slider[i].style.transitionDuration = time + 's';
					this.slider[i].style.WebkitTransitionDuration = time + 's';
				}
				this.$els.process.style.transitionDuration = time + 's';
				this.$els.process.style.WebkitTransitionDuration = time + 's';
			} else {
				this.slider.style.transitionDuration = time + 's';
				this.slider.style.WebkitTransitionDuration = time + 's';
				this.$els.process.style.transitionDuration = time + 's';
				this.$els.process.style.WebkitTransitionDuration = time + 's';
			}
		},
		getValue: function getValue() {
			return this.val;
		},
		getIndex: function getIndex() {
			if (Array.isArray(this.currentValue)) {
				if (this.data) {
					return this.currentValue;
				} else {
					return [(this.currentValue[0] - this.minimum) / this.spacing, (this.currentValue[1] - this.minimum) / this.spacing];
				}
			} else {
				return (this.currentValue - this.minimum) / this.spacing;
			}
		},
		refresh: function refresh() {
			this.size = this.direction === 'vertical' ? this.$els.elem.offsetHeight : this.$els.elem.offsetWidth;
			this.setPosition(0);
		}
	},
	created: function created() {
		window.addEventListener('resize', this.refresh);
	},
	ready: function ready() {
		this.size = this.direction === 'vertical' ? this.$els.elem.offsetHeight : this.$els.elem.offsetWidth;
		this.setValue(this.value);
		this.bindEvents();
	},
	destroyed: function destroyed() {
		this.unbindEvents();
	}
});