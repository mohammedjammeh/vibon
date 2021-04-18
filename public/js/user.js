/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 196);
/******/ })
/************************************************************************/
/******/ ({

/***/ 196:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(5);


/***/ }),

/***/ 5:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
var user = {
    id: null,
    deviceID: null,
    myVibesIDs: [],
    memberOfVibesIDs: [],

    routes: {
        'attributes': 'user/attributes'
    },

    getAttributes: function getAttributes() {
        var _this = this;

        return axios.get(this.routes.attributes).then(function (response) {
            _this.id = response.data.id;
            _this.deviceID = response.data.device_id;
            _this.myVibesIDs = response.data.my_vibes;
            _this.memberOfVibesIDs = response.data.member_of_vibes;
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    getAccessToken: function getAccessToken() {
        var _this2 = this;

        var now = new Date();
        now.setHours(now.getHours() - 1);
        var oneHourAgo = now.getTime();

        return new Promise(function (resolve, reject) {
            if (localStorage['token_set_at'] >= oneHourAgo) {
                resolve(localStorage['access_token']);
            } else {
                return axios.get(_this2.routes.attributes).then(function (response) {
                    localStorage['token_set_at'] = new Date(response.data.token_set_at).getTime();
                    localStorage['access_token'] = response.data.access_token;
                    resolve(localStorage['access_token']);
                }).catch(function (error) {
                    reject(error.response.data.errors);
                });
            }
        });
    },
    updateVibesIDs: function updateVibesIDs(vibe) {
        this.removeVibeFromVibesIDs(vibe.id);
        this.addVibeToVibesIDs(vibe);
    },
    removeVibeFromVibesIDs: function removeVibeFromVibesIDs(vibeID) {
        this.myVibesIDs = this.myVibesIDs.filter(function (id) {
            return id !== vibeID;
        });
        this.memberOfVibesIDs = this.memberOfVibesIDs.filter(function (id) {
            return id !== vibeID;
        });
    },
    addVibeToVibesIDs: function addVibeToVibesIDs(vibe) {
        if (vibe.auto_dj) {
            this.myVibesIDs.push(vibe.id);
            return;
        }
        this.memberOfVibesIDs.push(vibe.id);
    },
    sortVibesIDsOrder: function sortVibesIDsOrder(vibes) {
        this.sortMemberOfVibesIDs(vibes);
        this.sortMyVibesIDs(vibes);
    },
    sortMemberOfVibesIDs: function sortMemberOfVibesIDs(vibes) {
        var _this3 = this;

        var sortedMemberOfVibesIDs = vibes.map(function (vibe) {
            return _this3.memberOfVibesIDs.filter(function (vibeID) {
                return vibeID === vibe.id;
            })[0];
        });
        this.memberOfVibesIDs = sortedMemberOfVibesIDs.filter(function (vibeID) {
            return vibeID !== undefined;
        });
    },
    sortMyVibesIDs: function sortMyVibesIDs(vibes) {
        var _this4 = this;

        var sortedMyVibesIDs = vibes.map(function (vibe) {
            return _this4.myVibesIDs.filter(function (vibeID) {
                return vibeID === vibe.id;
            })[0];
        });
        this.myVibesIDs = sortedMyVibesIDs.filter(function (vibeID) {
            return vibeID !== undefined;
        });
    }
};

window.user = user;
/* harmony default export */ __webpack_exports__["default"] = (user);

/***/ })

/******/ });