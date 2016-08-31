webpackJsonp([1],{

/***/ 4:
/***/ function(module, exports, __webpack_require__) {

	'use strict';
	
	var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();
	
	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
	
	var $ = __webpack_require__(1);
	
	module.exports = function () {
	  function SampleModule($el) {
	    _classCallCheck(this, SampleModule);
	
	    this.$el = $el;
	    this.method(this.$el);
	  }
	
	  _createClass(SampleModule, [{
	    key: 'method',
	    value: function method($element) {
	      console.log($element);
	    }
	  }]);
	
	  return SampleModule;
	}();

/***/ }

});
//# sourceMappingURL=1.bundle.js.map