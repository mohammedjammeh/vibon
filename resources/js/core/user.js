const user = {
    id: null,
    deviceID: null,
    myVibesIDs: [],
    memberOfVibesIDs: [],

    routes: {
        'attributes': 'user/attributes',
    },

    getAttributes() {
        return axios.get(this.routes.attributes)
            .then(response => {
                this.id = response.data.id;
                this.deviceID = response.data.device_id;
                this.myVibesIDs = response.data.my_vibes;
                this.memberOfVibesIDs = response.data.member_of_vibes;
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
        this.myVibesIDs = this.myVibesIDs.filter(id => id !== vibeID);
        this.memberOfVibesIDs = this.memberOfVibesIDs.filter(id => id !== vibeID);
    },

    addVibeToVibesIDs(vibe) {
        if(vibe.auto_dj) {
            this.myVibesIDs.push((vibe.id));
            return;
        }
        this.memberOfVibesIDs.push(vibe.id);
    },

    sortVibesIDsOrder(vibes) {
        this.sortMemberOfVibesIDs(vibes);
        this.sortMyVibesIDs(vibes);
    },

    sortMemberOfVibesIDs(vibes) {
        let sortedManuelVibesIDs = vibes.map((vibe) => {
            return this.memberOfVibesIDs.filter(vibeID => vibeID === vibe.id)[0];
        });
        this.memberOfVibesIDs = sortedManuelVibesIDs.filter((vibeID) => vibeID !== undefined);
    },

    sortMyVibesIDs(vibes) {
        let sortedAutoVibesIDs = vibes.map((vibe) => {
            return this.myVibesIDs.filter(vibeID => vibeID === vibe.id)[0];
        });
        this.myVibesIDs = sortedAutoVibesIDs.filter((vibeID) => vibeID !== undefined);
    }
};

window.user = user;
export default user;