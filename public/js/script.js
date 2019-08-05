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
/******/ 	return __webpack_require__(__webpack_require__.s = 44);
/******/ })
/************************************************************************/
/******/ ({

/***/ 44:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(45);


/***/ }),

/***/ 45:
/***/ (function(module, exports) {

// $(document).ready(function() {
//     alert('lol');
//
//     window.onSpotifyWebPlaybackSDKReady = () => {
//         const token = 'BQBhAWTh06G1Fba3tmDyDXTvlgGGSmTQAXLi89k-Nf4eCYIPZfVdl5SZACXiIJTX6l1rV1ZkiK0_oMXpsy02VIsrUYQZxSQ-wUmJBXWFT3D1CoIMJVVSXuidpTAglrc753soZ59xCb4IH0_9W3G4-7lFsSknuk4jsUg9BU3XnvMzq4CPby-kRc3RtkCLrDcfEhbdW0REmjmBK1HUjZ72mBvjD1YPf6eFb5SqPh2fq0Xntz7-sueWFfJa6mwTpADiHxHBGkkVUSw';
//         const player = new Spotify.Player({
//             name: 'Vibon',
//             getOAuthToken: cb => { cb(token); }
//         });
//
//         // Error handling
//         player.addListener('initialization_error', ({ message }) => { console.error(message); });
//         player.addListener('authentication_error', ({ message }) => { console.error(message); });
//         player.addListener('account_error', ({ message }) => { console.error(message); });
//         player.addListener('playback_error', ({ message }) => { console.error(message); });
//
//         // Playback status updates
//         player.addListener('player_state_changed', state => { console.log(state); });
//
//         // Ready
//         player.addListener('ready', ({ device_id }) => {
//             console.log('Ready with Device ID', device_id);
//             $.ajax({
//                 type: 'GET',
//                 url: '/playback/play/1',
//                 data: {'device_id':device_id},
//                 success: function(){
//                     console.log('playing');
//                 }
//             });
//         });
//
//         // Not Ready
//         player.addListener('not_ready', ({ device_id }) => {
//             console.log('Device ID has gone offline', device_id);
//         });
//
//         // Connect to the player!
//         player.connect();
//     };
//
// });

/***/ })

/******/ });