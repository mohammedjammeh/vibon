import vibes from './vibes';

const track = {
    categories: {
        'playlist': 'playlist',
        'not_on_vibon': 'not_on_vibon',
        'not_on_playlist': 'not_on_playlist',
        'pending_to_attach': 'pending_to_attach',
        'pending_to_detach': 'pending_to_detach',
    },

    category(vibeID, trackApiId, category) {
        let vibe = vibes.all.find((vibe) => vibe.id === vibeID);
        let tracksNotOnVibon = vibe.api_tracks.not_on_vibon;
        let tracksNotOnVibonIDs = tracksNotOnVibon.map((track) => track.id);

        if(tracksNotOnVibonIDs.includes(trackApiId)) {
            return this.categories.not_on_vibon;
        }

        return category;
    }
};

export default track;