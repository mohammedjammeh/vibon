        $genresNumOfTracks = [];
        $genresTracks = [];
        $genresList = collect([]);

        // All the Genres of all the tracks that belong Vibe
        // Genres for this Vibe
        $genresList = Genre::
        // Generate genres array
        foreach ($vibe->autoTracks() as $track) {
            $genresList = $genresList->mapToGroups();
        }


        dd($genresList);


        // Update counts
        foreach ($vibe->autoTracks() as $track) {
            foreach ($track->genres as $genre) {
                if (isset($genresNumOfTracks[$genre->name])) {
                    $genresNumOfTracks[$genre->name]++;
                } else {
                    $genresNumOfTracks[$genre->name] = 1;
                }
            }
        }