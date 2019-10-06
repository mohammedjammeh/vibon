<?php 

namespace App\MusicAPI;

interface InterfaceAPI
{
    function authorise();
    function setAuthenticatedUserAccessToken();
    function refreshUserAccessToken();
    function setUnauthenticatedUserAccessToken($accessToken);
    function getUser();
    function getUserDevices();
    function search($name);

    function createPlaylist($name, $description);
    function updatePlaylist($id, $name, $description);
    function getPlaylist($id);
    function deletePlaylist($id);
    function addTracksToPlaylist($playlistId, $tracksId);
    function replaceTracksOnPlaylist($playlistId, $tracksId);
    function reorderPlaylistTracks($playlistId, $rangeStart, $insertBefore);

    function getTrack($id);
    function getUserTopTracks();
    function getUserRecentTracks();
    function getArtist($id);
    function getTrackRecommendations($options);

    function getPlaybackCurrentTrack();
}