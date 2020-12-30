import _ from 'lodash';

let Search = {
    route: '',
    tracks: {},
    playingTrack: '',

    setRoute: function (input) {
        this.route = '/search/' + input;
    },

    searchInput: _.debounce(function(input) {
        this.setRoute(input);
        return new Promise((resolve, reject) => {
            axios.get(this.route)
                .then(response => {
                    this.tracks = response.data;
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response.data.errors);
                });
        });
    }, 2000)
}

export default Search;
