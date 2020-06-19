<?php
namespace SpotifyRandomAlbum;

use Exception;

class RandomAlbumException extends Exception {
    public function __construct($code, $message = null) {
        parent::__construct($message, $code);
    }
}