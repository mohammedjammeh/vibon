let User = {
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

export default User;