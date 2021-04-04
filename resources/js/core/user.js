const user = {
    id: null,
    deviceID: null,
    autoVibesIDs: [],
    manualVibesIDs: [],

    routes: {
        'attributes': 'user/attributes',
    },

    getAttributes() {
        return axios.get(this.routes.attributes)
            .then(response => {
                this.id = response.data.id;
                this.deviceID = response.data.device_id;
                this.autoVibesIDs = response.data.auto_vibes;
                this.manualVibesIDs = response.data.manual_vibes;
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

    updateVibesIDs(vibe) {
        this.removeVibeFromVibesIDs(vibe.id);
        this.addVibeToVibesIDs(vibe);
    },

    removeVibeFromVibesIDs(vibeID) {
        this.autoVibesIDs = this.autoVibesIDs.filter(id => id !== vibeID);
        this.manualVibesIDs = this.manualVibesIDs.filter(id => id !== vibeID);
    },

    addVibeToVibesIDs(vibe) {
        if(vibe.auto_dj) {
            this.autoVibesIDs.push((vibe.id));
            return;
        }
        this.manualVibesIDs.push(vibe.id);
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