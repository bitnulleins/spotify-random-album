# Spotify Random Album
Creates with given search query a random album playlist.

* Works with any search query
* Two types of random albums (once and repeatble)
* Search for >50 possible albums
* Clear the old playlist tracks automatically

# Requirments

* PHP 7.0 or later.
* PHP cURL extension (Usually included with PHP).
* [Spotify Web API Library](https://github.com/jwilsson/spotify-web-api-php) from Jonathan Wilsson

# Examples

For example I created with this code some **german** random audio playbook playlists:

|  Sherlock Holmes | Three Investigators | Thriller | Fairytale |
| ------------- | ------------- | ------------- | ------------- |
| <a target="_blank" href="https://open.spotify.com/playlist/6rz59B15Onz6fPcECfAZF6?si=hXniVF0DTuGWOQNcRznOvg"><img src="https://www.bit01.de/wp-content/uploads/2020/01/cover_sherlock-400x400.jpg" width="150" /></a>  | <a target="_blank" href="https://open.spotify.com/playlist/2PL8CZFuBbr320axNXzaKj?si=kFwKmt0oQgmjfZbyHUmwQw"><img src="https://www.bit01.de/wp-content/uploads/2020/01/DreiFragezeichen-400x400.jpg" width="150" />  | <a target="_blank" href="https://open.spotify.com/playlist/2jbVmEetA84pL05GPjxm50?si=VQSGDs72Tti5CDPN9XfLnQ"><img src="https://www.bit01.de/wp-content/uploads/2020/01/krimi-400x400.jpg" width="150" />  | <a target="_blank" href="https://open.spotify.com/playlist/6NtFIpqAvZzBSCuc0yljD3?si=Y1uzFD8TShWzJ_3mPP2mjQ"><img src="https://www.bit01.de/wp-content/uploads/2020/01/märchenderwoche-400x400.jpg" width="150" />  |

# Preparation 

1. Create an app at [Spotify’s developer site](https://developer.spotify.com)
2. Create new public/private album on Spotify and copy playlist uri via share button
3. Install *composer* and this package:
```composer require bitnulleins/spotify-random-album```
4. Create a [search query](https://support.spotify.com/us/article/search/) for Spotify.

# Usage

Before using the Spotify Web API, you'll need to create an app at Spotify’s developer site.

```php
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
        "YOUR_PLAYLIST_URI"
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
```

# Extras

### Use an array of albums instead of search query

```php
$albums = [ "albumID1", "albumID2", ... ];

(new SpotifyRandomAlbum\RandomAlbum($api))->createRandomAlbum(
    'YOUR SPOTIFY SEARCH QUERY',
    "SPOTIFY_PLAYLIST_URI",
    array('albums'=>$album_list)
);
```

### For more than one search query per playlist use an random array:

```php
$queries = [
        "searchQuery1",
        "searchQuery2",
];
$rand_query = $queries[array_rand($queries)];
...
```

# License

GNU GENERAL PUBLIC LICENSE V2
