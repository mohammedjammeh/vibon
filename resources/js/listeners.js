import vibes from './core/vibes.js';

// Vibe
Echo.channel('vibe.created')
    .listen('VibeCreated', (data) => {
        vibes.get(data.vibe)
            .then((response) => {
                vibes.all.push(response.vibe);
            });
    });

Echo.channel('vibe.updated')
    .listen('VibeUpdated', (data) => {
        vibes.get(data.vibe)
            .then((response) => {
                vibes.all = vibes.all.map((vibe) => {
                    return vibe.id === response.vibe.id
                        ? response.vibe
                        : vibe;
                });
                vibes.updateShowData();
            });
    });

Echo.channel('vibe.deleted')
    .listen('VibeDeleted', (data) => {
        if (vibes.showID === data.vibe) {
            vibes.show = {};
            vibes.deletedMessage = data.message;
        }
        vibes.all = vibes.all.filter(vibe => vibe.id !== data.vibe);
    });