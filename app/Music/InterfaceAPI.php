<?php 

namespace App\Music;

interface InterfaceAPI
{
    function authorise();
    function setAuthenticatedUserAccessToken();
    function refreshUserAccessToken();
    function setUnauthenticatedUserAccessToken($accessToken);
    function getUser();
    function search($name);

    function createPlaylist($name);
    function updatePlaylist($id, $name);
    function getPlaylist($id);
    function deletePlaylist($id);
    function addTracksToPlaylist($playlistId, $tracksId);
    function replaceTracksOnPlaylist($playlistId, $tracksId);

    function getTrack($id);
    function getUserTopTracks();
    function getUserRecentTracks();
    function getArtist($id);
    function getTrackRecommendations($options);
}