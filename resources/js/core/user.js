let user = {
    vibesIDs: [],

    routes: {
        'userVibes': '/user/vibes',
    },

    getVibesIDs() {
        return axios.get(this.routes.userVibes)
            .then(response => {
                this.vibesIDs = response.data;
            })
            .catch(errors => console.log(errors));
    },

    getAccessToken() {
        let now = new Date();
        now.setHours(now.getHours() - 1);
        let oneHourAgo = now.getTime();

        if(localStorage['token_set_at'] >= oneHourAgo) {
            return localStorage['access_token'];
        } else {
            let user = $.ajax({
                type: 'GET',
                dataType: 'json',
                async: false,
                url: '/playback-user',
                success: function(data) {
                    return data;
                }
            });

            let userAttributes = JSON.parse(user.responseText);
            localStorage['token_set_at'] = new Date(userAttributes['token_set_at']).getTime();
            localStorage['access_token'] = userAttributes['access_token'];
            return localStorage['access_token'];
        }
    },

    updateVibesIDs(vibe) {
        if(!parseInt(vibe.auto_dj)) {
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