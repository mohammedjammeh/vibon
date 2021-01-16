import vibes from './vibes';
import user from './user';

const pendingTrack = {
    vibes: vibes,
    user: user,

    get(trackID, vibeID) {
        let vibe = this.vibes.all.filter((vibe) => vibe.id === vibeID)[0];

        return vibe.pending_tracks.find((pendingTrack) => {
            return pendingTrack.vibe_id === vibeID  &&
                pendingTrack.track_id === trackID
        });
    },

    isPendedByUser(pendingTrack) {
        return pendingTrack.user_id === this.user.id;
    },

    canBeRemovedByUser(pendingTrack, vibeID) {
        return this.isPendedByUser(pendingTrack) || this.vibes.ownedByUser(vibeID);
    }
};

export default pendingTrack;