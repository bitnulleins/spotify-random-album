<?php
require __DIR__ . '/vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    'CLIENT_ID',
    'CLIENT_SECRET',
    'REDIRECT_URI'
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    (new SpotifyRandomAlbum\RandomAlbum($api))->createRandomAlbum(
        'YOUR SPOTIFY SEARCH QUERY',
        "SPOTIFY_PLAYLIST_URI"
    );
} else {
    $options = [
        'scope' => [
            'playlist-modify-private',
            'playlist-modify-public'
        ],
    ];

    header('Location: ' . $session->getAuthorizeUrl($options));
    die();
}