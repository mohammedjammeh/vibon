<?php

namespace App\MusicAPI\Fake;

class Session
{
    protected $accessToken = '1902332n2323nn23kweo23jee';
    protected $clientId = '2db7a6fa83cf4f8c828bfffee150ced5';
    protected $clientSecret = '54dbded59705461c89af7c89256a0c01';
    protected $expirationTime = 0;
    protected $redirectUri = "http://vibin.localhost/spotify";
    protected $refreshToken = '1289128921u21h230923nei32mm';

    public function requestAccessToken($code)
    {
        if (isset($code)) {
            return true;
        }
        return false;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

}
