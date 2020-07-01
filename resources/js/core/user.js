const user = {
    id: '',
    vibesIDs: [],
    notifications: [],

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
                this.vibesIDs = response.data;
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
            })
            .catch(errors => { console.log(errors)});
    },

    updateVibesIDs(vibe) {
        if(!vibe.auto_dj) {
            this.vibesIDs.push(vibe.id);
        }  else {
            this.vibesIDs = this.vibesIDs.filter(id => id !== vibe.id);
        }
    },

    allVibesIDsExcept(id) {
       return this.vibesIDs.filter(vibeID => vibeID !== id);
    },
};

window.user = user;
export default user;