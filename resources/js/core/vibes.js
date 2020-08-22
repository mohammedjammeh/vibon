import User from "./user";

let Vibes = {
    all: [],
    show: {},
    showID: '',
    message: '',
    deletedMessage: '',
    loading: true,

    user: User,
    playingTracks: {},
    playingID: '',

    routes: {
        'index': '/vibe',
        'create': '/vibe',
        'show': function (vibeID) {
            return '/vibe/' + vibeID;
        },
        'update': function (vibeID) {
            return '/vibe/' + vibeID;
        },
        'delete': function (vibeID) {
            return '/vibe/' + vibeID;
        },

        'autoRefresh': function (vibeID) {
            return '/auto-vibe/' + vibeID;
        },
        'syncVibe': function (vibeID) {
            return 'sync/vibe/' + vibeID;
        },
        'syncPlaylist': function (vibeID) {
            return 'sync/playlist/' + vibeID;
        },

        'acceptJoinRequest': function (requestID) {
            return '/join-request/accept/' + requestID;
        },
        'rejectJoinRequest': function (requestID) {
            return '/join-request/reject/' + requestID;
        },

        'sendJoinRequest': function (vibeID) {
            return '/join-request/vibe/' + vibeID;
        },
        'cancelJoinRequest': function (requestID) {
            return '/join-request/delete/' + requestID;
        },
        'leaveVibe': function (vibeID) {
            return '/user-vibe/vibe/' + vibeID;
        },
        'joinVibe': function (vibeID) {
            return '/user-vibe/vibe/' + vibeID;
        },

        'removeUser': function (vibeID, userID) {
            return '/user-vibe/vibe/' + vibeID + '/user/' + userID;
        },

        'removeTrack': function (vibeID, trackID) {
            return '/track-vibe/vibe/' + vibeID + '/track/' + trackID;
        },
        'addTrack': function (vibeID, trapApiId) {
            return '/track-vibe/vibe/' + vibeID + '/track-api/' + trapApiId;
        },

        'upvoteTrack': function (vibeID, trackID) {
            return '/vote/vibe/' + vibeID + '/track/' + trackID;
        },
        'downvoteTrack': function (vibeID, trackID) {
            return '/vote/vibe/' + vibeID + '/track/' + trackID;
        }
    },

    getAll() {
        return new Promise((resolve, reject) => {
            return axios.get(this.routes.index)
                .then(response => {
                    let vibesData = response.data;
                    for(let key in vibesData) {
                        if(vibesData.hasOwnProperty(key)) {
                            this.all.push(vibesData[key].vibe);
                        }
                    }
                    this.updatePlayingTracksData();
                    this.updateShowData();
                    this.loading = false;
                    resolve(vibesData);
                })
                .catch(error => {
                    reject(error.response.data.errors);
                });
        });
    },

    create(form) {
        return form.post(this.routes.create)
            .then(response => {
                this.all.push(response.vibe);
                this.sortVibesOrder();
                this.user.addVibeToVibesIDs(response.vibe);
            })
            .catch(errors => console.log(errors));
    },

    get(vibeID) {
        return new Promise((resolve, reject) => {
            return axios.get(this.routes.show(vibeID))
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response.data.errors);
                });
        });
    },

    display(vibeID) {
        this.showID = parseInt(vibeID);

        if (Object.keys(this.all).length > 0) {
            this.all.forEach(vibe => {
                if (vibe.id === this.showID) {
                    this.show = vibe;
                }
            })
        }
    },

    update(form, vibeID) {
        return form.update(this.routes.update(vibeID))
            .then(response => {
                this.all = this.all.map((vibe) => {
                    if(vibe.id === response.vibe.id) {
                        this.show = response.vibe;
                        this.user.updateVibesIDs(response.vibe);
                        return response.vibe;
                    }
                    return vibe;
                });

                this.sortVibesOrder();
            })
            .catch(errors => console.log(errors));
    },

    delete(form, vibeID) {
        form.delete(this.routes.delete(vibeID))
            .then(response => {
                this.all = this.all.filter(vibe => vibe.id !== vibeID);
                this.user.removeVibeFromVibesIDs(vibeID);
                this.show = {};
                this.deletedMessage = response.message;
            })
            .catch(errors => console.log(errors));
    },

    autoRefresh(form, vibeID) {
        form.post(this.routes.autoRefresh(vibeID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    syncVibe(form, vibeID) {
        form.post(this.routes.syncVibe(vibeID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    syncPlaylist(form, vibeID) {
        form.post(this.routes.syncPlaylist(vibeID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    acceptJoinRequest(form, requestID) {
        form.delete(this.routes.acceptJoinRequest(requestID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    rejectJoinRequest(form, requestID) {
        form.delete(this.routes.rejectJoinRequest(requestID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    sendJoinRequest(form, vibeID) {
        form.post(this.routes.sendJoinRequest(vibeID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },
    
    cancelJoinRequest: function (form, requestID) {
        form.delete(this.routes.cancelJoinRequest(requestID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    joinVibe: function (form, vibeID) {
        form.post(this.routes.joinVibe(vibeID))
            .then(response => {
                this.updateData(response);
                this.sortVibesOrder();
                this.user.addVibeToVibesIDs(response.vibe);
            })
            .catch(errors => console.log(errors));
    },

    leaveVibe: function (form, vibeID) {
        form.delete(this.routes.leaveVibe(vibeID))
            .then(response => {
                this.updateData(response);
                this.user.removeVibeFromVibesIDs(vibeID);
            })
            .catch(errors => console.log(errors));
    },

    removeUser: function (form, vibeID, userID) {
        form.delete(this.routes.removeUser(vibeID, userID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    removeTrack: function (form, vibeID, trackID) {
        form.delete(this.routes.removeTrack(vibeID, trackID))
            .then(response => {
                this.all = this.all.map((vibe) => {
                    this.updateTracksVibesDataForRemovedTrack(vibe, trackID, response);
                    return vibe.id === response.vibe.id ? response.vibe : vibe;
                });
                this.updateShowData();
            })
            .catch(errors => console.log(errors));
    },

    addTrack: function (form, vibeID, trackApiId) {
        form.post(this.routes.addTrack(vibeID, trackApiId))
            .then(response => {
                this.all = this.all.map((vibe) => {
                    this.updateTracksVibesDataForAddedTrack(vibe, trackApiId, response);
                    return vibe.id === response.vibe.id ? response.vibe : vibe;
                });
                this.updateShowData();
            })
            .catch(errors => console.log(errors));
    },
    
    upvoteTrack: function (form, vibeID, trackID) {
        form.post(this.routes.upvoteTrack(vibeID, trackID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    downvoteTrack: function (form, vibeID, trackID) {
        form.delete(this.routes.downvoteTrack(vibeID, trackID))
            .then(response => {
                this.updateData(response);
            })
            .catch(errors => console.log(errors));
    },

    readyToShow() {
        return Object.keys(this.show).length > 0;
    },

    getVibeName(vibeID) {
        if (this.all.length) {
            return this.all.find(vibe => vibe.id === vibeID).name;
        }
    },

    updateShowData() {
        if(this.showID !== '') {
            this.all.forEach(vibe => {
                if (vibe.id === this.showID) {
                    this.show = vibe;
                }
            })
        }
    },

    updatePlayingTracksData() {
        let playingVibesTracks = {};
        this.all.forEach(vibe => {
            playingVibesTracks[vibe.id] = '';
        });
        this.playingTracks = playingVibesTracks;
    },

    updateData(response) {
        this.all = this.all.map((vibe) => {
            if(vibe.id === response.vibe.id) {
                this.show = response.vibe;
                this.message = response.message;
                setTimeout(() => this.message = '', 20000);
                return response.vibe;
            }
            return vibe;
        });
    },

    updateVibeDataForUpdatedVibe(updatedVibe) {
        this.all = this.all.map((vibe) => {
            return vibe.id === updatedVibe.id
                ? updatedVibe
                : vibe;
        });
    },

    updateTracksVibesDataForRemovedTrack(vibe, trackID, response) {
        if(!vibe.auto_jd) {
            vibe.api_tracks.forEach(track => {
                if(track.vibon_id === trackID) {
                    let trackVibeIndex = track.vibes.indexOf(response.vibe.id);
                    if (trackVibeIndex !== -1) {
                        track.vibes.splice(trackVibeIndex, 1);
                    }
                }
            });
        }
    },

    updateTracksVibesDataForAddedTrack(vibe, trackApiId, response) {
        if(!vibe.auto_jd) {
            vibe.api_tracks.forEach(track => {
                if(track.id === trackApiId) {
                    track.vibes.push(response.vibe.id);
                }
            });
        }
    },

    sortVibesOrder() {
        let vibesNames = this.all.map((vibe) => vibe.name);
        vibesNames.sort();
        this.all = vibesNames.map((name) => {
            let filteredVibes = this.all.filter(vibe => vibe.name === name);
            return filteredVibes[0];
        });

        this.user.sortVibesIDsOrder(this.all);
    },

    isEmpty() {
        return Object.keys(this.all).length === 0;
    }
};

export default Vibes;