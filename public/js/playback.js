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
/******/ 	return __webpack_require__(__webpack_require__.s = 92);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
var user = {
    vibesIDs: [],
    notifications: [],

    routes: {
        'vibes': '/user/vibes',
        'attributes': 'user/attributes',
        'notifications': 'user/notifications'
    },

    getVibesIDs: function getVibesIDs() {
        var _this = this;

        return axios.get(this.routes.vibes).then(function (response) {
            _this.vibesIDs = response.data;
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
    getNotifications: function getNotifications() {
        var _this3 = this;

        return axios.get(this.routes.notifications).then(function (response) {
            _this3.notifications = response.data;
            console.log(response.data);
        }).catch(function (errors) {
            console.log(errors);
        });
    },
    updateVibesIDs: function updateVibesIDs(vibe) {
        if (!vibe.auto_dj) {
            this.vibesIDs.push(vibe.id);
        } else {
            this.vibesIDs = this.vibesIDs.filter(function (id) {
                return id !== vibe.id;
            });
        }
    },
    allVibesIDsExcept: function allVibesIDsExcept(id) {
        return this.vibesIDs.filter(function (vibeID) {
            return vibeID !== id;
        });
    }
};

window.user = user;
/* harmony default export */ __webpack_exports__["default"] = (user);

/***/ }),

/***/ 2:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__user__ = __webpack_require__(1);


var Vibes = {
    all: [],
    show: {},
    showID: '',
    message: '',
    deletedMessage: '',

    user: __WEBPACK_IMPORTED_MODULE_0__user__["default"],
    playingTracks: {},
    playingID: '',

    routes: {
        'index': '/vibe',
        'create': '/vibe',
        'show': function show(vibeID) {
            return '/vibe/' + vibeID;
        },
        'update': function update(vibeID) {
            return '/vibe/' + vibeID;
        },
        'delete': function _delete(vibeID) {
            return '/vibe/' + vibeID;
        },

        'autoRefresh': function autoRefresh(vibeID) {
            return '/auto-vibe/' + vibeID;
        },
        'syncVibe': function syncVibe(vibeID) {
            return 'sync/vibe/' + vibeID;
        },
        'syncPlaylist': function syncPlaylist(vibeID) {
            return 'sync/playlist/' + vibeID;
        },

        'acceptJoinRequest': function acceptJoinRequest(requestID) {
            return '/join-request/accept/' + requestID;
        },
        'rejectJoinRequest': function rejectJoinRequest(requestID) {
            return '/join-request/reject/' + requestID;
        },

        'sendJoinRequest': function sendJoinRequest(vibeID) {
            return '/join-request/vibe/' + vibeID;
        },
        'cancelJoinRequest': function cancelJoinRequest(requestID) {
            return '/join-request/delete/' + requestID;
        },
        'leaveVibe': function leaveVibe(vibeID) {
            return '/user-vibe/vibe/' + vibeID;
        },
        'joinVibe': function joinVibe(vibeID) {
            return '/user-vibe/vibe/' + vibeID;
        },

        'removeUser': function removeUser(vibeID, userID) {
            return '/user-vibe/vibe/' + vibeID + '/user/' + userID;
        },

        'removeTrack': function removeTrack(vibeID, trackID) {
            return '/track-vibe/vibe/' + vibeID + '/track/' + trackID;
        },
        'addTrack': function addTrack(vibeID, trapApiId) {
            return '/track-vibe/vibe/' + vibeID + '/track-api/' + trapApiId;
        },

        'upvoteTrack': function upvoteTrack(vibeID, trackID) {
            return '/vote/vibe/' + vibeID + '/track/' + trackID;
        },
        'downvoteTrack': function downvoteTrack(vibeID, trackID) {
            return '/vote/vibe/' + vibeID + '/track/' + trackID;
        }
    },

    getAll: function getAll() {
        var _this = this;

        return new Promise(function (resolve, reject) {
            return axios.get(_this.routes.index).then(function (response) {
                var vibesData = response.data;
                for (var key in vibesData) {
                    if (vibesData.hasOwnProperty(key)) {
                        _this.all.push(vibesData[key].vibe);
                    }
                }
                _this.updatePlayingTracksData();
                _this.updateShowData();
                resolve(vibesData);
            }).catch(function (error) {
                reject(error.response.data.errors);
            });
        });
    },
    create: function create(form) {
        var _this2 = this;

        return form.post(this.routes.create).then(function (response) {
            _this2.all.push(response.vibe);
            _this2.user.updateVibesIDs(response.vibe);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    get: function get(vibeID) {
        var _this3 = this;

        return new Promise(function (resolve, reject) {
            return axios.get(_this3.routes.show(vibeID)).then(function (response) {
                resolve(response.data);
            }).catch(function (error) {
                reject(error.response.data.errors);
            });
        });
    },
    display: function display(vibeID) {
        var _this4 = this;

        this.showID = parseInt(vibeID);

        if (Object.keys(this.all).length > 0) {
            this.all.forEach(function (vibe) {
                if (vibe.id === _this4.showID) {
                    _this4.show = vibe;
                }
            });
        }
    },
    update: function update(form, vibeID) {
        var _this5 = this;

        return form.update(this.routes.update(vibeID)).then(function (response) {
            _this5.all = _this5.all.map(function (vibe) {
                if (vibe.id === response.vibe.id) {
                    _this5.show = response.vibe;
                    _this5.user.updateVibesIDs(response.vibe);
                    return response.vibe;
                }
                return vibe;
            });
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    delete: function _delete(form, vibeID) {
        var _this6 = this;

        form.delete(this.routes.delete(vibeID)).then(function (response) {
            _this6.all = _this6.all.filter(function (vibe) {
                return vibe.id !== vibeID;
            });
            _this6.user.vibesIDs = _this6.user.vibesIDs.filter(function (id) {
                return id !== vibeID;
            });
            _this6.show = {};
            _this6.deletedMessage = response.message;
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    autoRefresh: function autoRefresh(form, vibeID) {
        var _this7 = this;

        form.post(this.routes.autoRefresh(vibeID)).then(function (response) {
            _this7.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    syncVibe: function syncVibe(form, vibeID) {
        var _this8 = this;

        form.post(this.routes.syncVibe(vibeID)).then(function (response) {
            _this8.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    syncPlaylist: function syncPlaylist(form, vibeID) {
        var _this9 = this;

        form.post(this.routes.syncPlaylist(vibeID)).then(function (response) {
            _this9.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    acceptJoinRequest: function acceptJoinRequest(form, requestID) {
        var _this10 = this;

        form.delete(this.routes.acceptJoinRequest(requestID)).then(function (response) {
            _this10.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    rejectJoinRequest: function rejectJoinRequest(form, requestID) {
        var _this11 = this;

        form.delete(this.routes.rejectJoinRequest(requestID)).then(function (response) {
            _this11.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },
    sendJoinRequest: function sendJoinRequest(form, vibeID) {
        var _this12 = this;

        form.post(this.routes.sendJoinRequest(vibeID)).then(function (response) {
            _this12.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },


    cancelJoinRequest: function cancelJoinRequest(form, requestID) {
        var _this13 = this;

        form.delete(this.routes.cancelJoinRequest(requestID)).then(function (response) {
            _this13.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    joinVibe: function joinVibe(form, vibeID) {
        var _this14 = this;

        form.post(this.routes.joinVibe(vibeID)).then(function (response) {
            _this14.updateData(response);
            _this14.user.updateVibesIDs(response.vibe);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    leaveVibe: function leaveVibe(form, vibeID) {
        var _this15 = this;

        form.delete(this.routes.leaveVibe(vibeID)).then(function (response) {
            _this15.updateData(response);
            _this15.user.vibesIDs = _this15.user.vibesIDs.filter(function (id) {
                return id !== vibeID;
            });
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    removeUser: function removeUser(form, vibeID, userID) {
        var _this16 = this;

        form.delete(this.routes.removeUser(vibeID, userID)).then(function (response) {
            _this16.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    removeTrack: function removeTrack(form, vibeID, trackID) {
        var _this17 = this;

        form.delete(this.routes.removeTrack(vibeID, trackID)).then(function (response) {
            _this17.all = _this17.all.map(function (vibe) {
                _this17.updateTracksVibesDataForRemovedTrack(vibe, trackID, response);
                return vibe.id === response.vibe.id ? response.vibe : vibe;
            });
            _this17.updateShowData();
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    addTrack: function addTrack(form, vibeID, trackApiId) {
        var _this18 = this;

        form.post(this.routes.addTrack(vibeID, trackApiId)).then(function (response) {
            _this18.all = _this18.all.map(function (vibe) {
                _this18.updateTracksVibesDataForAddedTrack(vibe, trackApiId, response);
                return vibe.id === response.vibe.id ? response.vibe : vibe;
            });
            _this18.updateShowData();
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    upvoteTrack: function upvoteTrack(form, vibeID, trackID) {
        var _this19 = this;

        form.post(this.routes.upvoteTrack(vibeID, trackID)).then(function (response) {
            _this19.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    downvoteTrack: function downvoteTrack(form, vibeID, trackID) {
        var _this20 = this;

        form.delete(this.routes.downvoteTrack(vibeID, trackID)).then(function (response) {
            _this20.updateData(response);
        }).catch(function (errors) {
            return console.log(errors);
        });
    },

    readyToShow: function readyToShow() {
        return Object.keys(this.show).length > 0;
    },
    getVibeName: function getVibeName(vibeID) {
        if (this.all.length) {
            return this.all.find(function (vibe) {
                return vibe.id === vibeID;
            }).name;
        }
    },
    updateShowData: function updateShowData() {
        var _this21 = this;

        if (this.showID !== '') {
            this.all.forEach(function (vibe) {
                if (vibe.id === _this21.showID) {
                    _this21.show = vibe;
                }
            });
        }
    },
    updatePlayingTracksData: function updatePlayingTracksData() {
        var playingVibesTracks = {};
        this.all.forEach(function (vibe) {
            playingVibesTracks[vibe.id] = '';
        });
        this.playingTracks = playingVibesTracks;
    },
    updateData: function updateData(response) {
        var _this22 = this;

        this.all = this.all.map(function (vibe) {
            if (vibe.id === response.vibe.id) {
                _this22.show = response.vibe;
                _this22.message = response.message;
                setTimeout(function () {
                    return _this22.message = '';
                }, 20000);
                return response.vibe;
            }
            return vibe;
        });
    },
    updateVibeDataForUpdatedVibe: function updateVibeDataForUpdatedVibe(updatedVibe) {
        this.all = this.all.map(function (vibe) {
            return vibe.id === updatedVibe.id ? updatedVibe : vibe;
        });
    },
    updateTracksVibesDataForRemovedTrack: function updateTracksVibesDataForRemovedTrack(vibe, trackID, response) {
        if (!vibe.auto_jd) {
            vibe.api_tracks.forEach(function (track) {
                if (track.vibon_id === trackID) {
                    var trackVibeIndex = track.vibes.indexOf(response.vibe.id);
                    if (trackVibeIndex !== -1) {
                        track.vibes.splice(trackVibeIndex, 1);
                    }
                }
            });
        }
    },
    updateTracksVibesDataForAddedTrack: function updateTracksVibesDataForAddedTrack(vibe, trackApiId, response) {
        if (!vibe.auto_jd) {
            vibe.api_tracks.forEach(function (track) {
                if (track.id === trackApiId) {
                    track.vibes.push(response.vibe.id);
                }
            });
        }
    }
};

/* harmony default export */ __webpack_exports__["a"] = (Vibes);

/***/ }),

/***/ 4:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__vibes__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__search__ = __webpack_require__(5);



var playback = {
    vibes: __WEBPACK_IMPORTED_MODULE_0__vibes__["a" /* default */],
    search: __WEBPACK_IMPORTED_MODULE_1__search__["a" /* default */],

    player: {},
    show: false,
    paused: false,
    playingTrack: {},

    playVibe: function playVibe(_ref) {
        var playlist_uri = _ref.playlist_uri,
            track_uri = _ref.track_uri,
            _ref$playerInstance$_ = _ref.playerInstance._options,
            getOAuthToken = _ref$playerInstance$_.getOAuthToken,
            id = _ref$playerInstance$_.id;

        getOAuthToken(function (access_token) {
            fetch('https://api.spotify.com/v1/me/player/play?device_id=' + id, {
                method: 'PUT',
                body: JSON.stringify({
                    context_uri: playlist_uri,
                    offset: {
                        uri: track_uri
                    }
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + access_token
                }
            });
        });
    },

    playTracks: function playTracks(_ref2) {
        var tracks_uris = _ref2.tracks_uris,
            track_uri = _ref2.track_uri,
            _ref2$playerInstance$ = _ref2.playerInstance._options,
            getOAuthToken = _ref2$playerInstance$.getOAuthToken,
            id = _ref2$playerInstance$.id;

        getOAuthToken(function (access_token) {
            fetch('https://api.spotify.com/v1/me/player/play?device_id=' + id, {
                method: 'PUT',
                body: JSON.stringify({
                    uris: tracks_uris,
                    offset: {
                        uri: track_uri
                    }
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + access_token
                }
            });
        });
    },

    updateData: function updateData(state) {
        if (state) {
            this.show = true;
            this.paused = state['paused'];

            this.playingTrack = state['track_window']['current_track'];

            var trackID = this.playingTrack['linked_from']['id'] ? this.playingTrack['linked_from']['id'] : this.playingTrack['id'];

            var vibeURI = state['context']['uri'];

            if (vibeURI === null) {
                this.updateSearchPlayingTracks(trackID);
            } else {
                this.updateVibePlayingTracks(trackID, vibeURI);
            }
        }
    },
    updateSearchPlayingTracks: function updateSearchPlayingTracks(trackID) {
        this.search.playingTrack = trackID;
    },
    updateVibePlayingTracks: function updateVibePlayingTracks(trackID, vibeURI) {
        var _this = this;

        if (Object.keys(this.vibes.all).length > 0) {
            this.vibes.all.map(function (vibe) {
                if (vibe.uri === vibeURI) {
                    vibe.api_tracks.forEach(function (track) {
                        if (track.id === trackID) {
                            _this.vibes.playingTracks[vibe.id] = track.id;
                        }
                    });

                    _this.vibes.playingID = vibe.id;
                }
            });
        }
    },
    playOrResume: function playOrResume() {
        this.player.resume().then(function () {});
    },
    pause: function pause() {
        this.player.pause().then(function () {});
    },
    previous: function previous() {
        this.player.previousTrack().then(function () {});
    },
    next: function next() {
        this.player.nextTrack().then(function () {});
    }
};

window.playback = playback;
/* harmony default export */ __webpack_exports__["default"] = (playback);

/***/ }),

/***/ 5:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var Search = {
    route: '',
    tracks: {},
    playingTrack: '',

    setRoute: function setRoute(input) {
        this.route = '/search/' + input;
    },

    searchInput: function searchInput(input) {
        var _this = this;

        this.setRoute(input);
        return new Promise(function (resolve, reject) {
            axios.get(_this.route).then(function (response) {
                _this.tracks = response.data;
                resolve(response.data);
            }).catch(function (error) {
                reject(error.response.data.errors);
            });
        });
    }
};

/* harmony default export */ __webpack_exports__["a"] = (Search);

/***/ }),

/***/ 92:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(4);


/***/ })

/******/ });