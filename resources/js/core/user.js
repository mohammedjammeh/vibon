const user = {
    id: '',
    autoVibesIDs: [],
    manualVibesIDs: [],
    notifications: [],
    notificationsLoading: true,

    routes: {
        'vibes': '/user/vibes',
        'attributes': 'user/attributes',
        'notifications': 'user/notifications'
    },

    getID() {
        return axios.get(this.routes.attributes)
            .then(response => {
                this.id = response.data.id;
            })
            .catch(errors => console.log(errors));
    },

    getVibesIDs() {
        return axios.get(this.routes.vibes)
            .then(response => {
                this.autoVibesIDs = response.data.auto;
                this.manualVibesIDs = response.data.manual;
            })
            .catch(errors => console.log(errors));
    },

    getAccessToken() {
        let now = new Date();
        now.setHours(now.getHours() - 1);
        let oneHourAgo = now.getTime();

        return new Promise((resolve, reject) => {
            if(localStorage['token_set_at'] >= oneHourAgo) {
                resolve(localStorage['access_token']);
            } else {
                return axios.get(this.routes.attributes)
                    .then(response => {
                        localStorage['token_set_at'] = new Date(response.data.token_set_at).getTime();
                        localStorage['access_token'] = response.data.access_token;
                        resolve(localStorage['access_token']);
                    })
                    .catch(error => {
                        reject(error.response.data.errors);
                    });
            }
        });
    },

    getNotifications() {
        return axios.get(this.routes.notifications)
            .then(response => {
                this.notifications = response.data;
                this.notificationsLoading = false;
            })
            .catch(errors => { console.log(errors)});
    },

    notificationsIsEmpty() {
        return Object.keys(this.notifications).length === 0;
    },

    addVibeToVibesIDs(vibe) {
        if(vibe.auto_dj) {
            this.autoVibesIDs.push((vibe.id));
            return;
        }
        this.manualVibesIDs.push(vibe.id);
    },

    updateVibesIDs(vibe) {
        this.removeVibeFromVibesIDs(vibe.id);
        this.addVibeToVibesIDs(vibe);
    },

    removeVibeFromVibesIDs(vibeID) {
        this.autoVibesIDs = this.autoVibesIDs.filter(id => id !== vibeID);
        this.manualVibesIDs = this.manualVibesIDs.filter(id => id !== vibeID);
    },

    sortVibesIDsOrder(vibes) {
        this.sortManualVibesIDs(vibes);
        this.sortAutoVibesIDs(vibes);
    },

    sortManualVibesIDs(vibes) {
        let sortedManuelVibesIDs = vibes.map((vibe) => {
            return this.manualVibesIDs.filter(manualVibeID => manualVibeID === vibe.id)[0];
        });
        this.manualVibesIDs = sortedManuelVibesIDs.filter((vibeID) => vibeID !== undefined);
    },

    sortAutoVibesIDs(vibes) {
        let sortedAutoVibesIDs = vibes.map((vibe) => {
            return this.autoVibesIDs.filter(autoVibeID => autoVibeID === vibe.id)[0];
        });
        this.autoVibesIDs = sortedAutoVibesIDs.filter((vibeID) => vibeID !== undefined);
    }
};

window.user = user;
export default user;