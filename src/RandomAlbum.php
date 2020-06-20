<?php
namespace SpotifyRandomAlbum;

class RandomAlbum
{
    protected $api;
    protected $targetPlaylistID;
	protected $offset;
    protected $albums;

	/*
	* rand = Random album select
	* no_repeat = No repetition of same album
	*/
    const SELECT_METHOD = 'no_repeat';
    const OPTIONS = array('limit'=>'50');

    /**
     * RandomAlbum constructor. Set the SpotifyWebAPI.
     * @param $api
     * @param array $options
     */
    function __construct($api) {
        $this->api = $api;
    }

    /**
     * @param $searchQuery
     * @param $targetPlaylistID
     * @param array $options
     */
    function createRandomAlbum($searchQuery, $targetPlaylistID, $options = array()) {
        try {
            $this->targetPlaylistID = explode(':', $targetPlaylistID);
            $this->targetPlaylistID = end($this->targetPlaylistID );
            $this->offset = $options['offset'] ?: null;
            $this->albums = (array) $options['albums'] ?: null;

            $arrayOfURIs = $this->albums ?: $this->getAlbumID($searchQuery);
            $albumID = $this->selectAlbum($arrayOfURIs, self::SELECT_METHOD);
            $getNewTracks = $this->getTracksByAlbum($albumID);
            $this->clearPlaylist($this->targetPlaylistID);
            return $this->addPlaylist($this->targetPlaylistID, $getNewTracks);
        } catch (\Exception $e) {
            throw new RandomAlbumException(500, "Some undefined error occured.");
        }
    }

    /**
     * @param $searchQuery
     * @throws RandomAlbumException
     */
    private function getAlbumID($searchQuery) {
        $i = $this->offset;

        for($k = 0 ; $k < 10000; $k+=50) {
            $options = array('limit'=>50,'offset'=>$k);
            $items = $this->api->search($searchQuery,'album',$options);
            foreach($items->albums->items as $key=>$item) {
                $uriArray[$i] = $item->id;
                $i++;
            }
            if (sizeof($items->albums->items) < 50) {
                break;
            }
        }

        if (!is_array($uriArray)) throw new RandomAlbumException(404, "Album not found.");
        return $uriArray;
    }

    /**
     * @param $array
     * @param $option
     * @return mixed
     */
    private function selectAlbum($array, $option) {
        switch($option) {
            case 'random':
                $result = $array[array_rand($array)];
                break;
			case 'no_repeat':
                if (($key = array_search($this->getAlbumIDByPlaylist($this->targetPlaylistID), $array)) !== false) {
                    unset($array[$key]);
                }
				$result = $array[array_rand($array)];
				break;
        }
		return $result;
    }

    /**
     * @param $albumID
     * @return mixed
     */
    private function getTracksByAlbum($albumID) {
        $i = 0;
        for($k = 0 ; $k < 500; $k+=50) {
            $options = array('limit'=>50,'offset'=>$k);
            $items = $this->api->getAlbumTracks($albumID,$options);
            foreach($items->items as $key=>$item) {
                $uriArray[$i] = $item->id;
                $i++;
            }
            if (sizeof($items->items) < 50) {
                break;
            }
        }

        if (sizeof($uriArray) == 0) throw new RandomAlbumException(404, "No Tracks found.");
        return $uriArray;
    }

    /**
     * @param $playlistID
     * @return mixed
     */
    protected function clearPlaylist($playlistID) {
        $tracks = $this->getTracksByPlaylist($playlistID);
        if (!empty($tracks['tracks'])) {
            return $this->api->deletePlaylistTracks($playlistID, $tracks);
        }
    }

    /**
     * @param $playlistID
     * @param $tracks
     * @return mixed
     */
    protected function addPlaylist($playlistID, $tracks) {
        $this->api->addPlaylistTracks($playlistID, array_slice($tracks, 0, 100));
        if (sizeof($tracks) > 100) {
            $tailTracks = array_splice($tracks, 0, 100);
            $this->addPlaylist($playlistID, $tailTracks);
        }
    }

    /**
     * @param $playlistID
     * @return mixed
     */
    private function getTracksByPlaylist($playlistID) {
        // offset: Skip first (1) track -> intro
        $items = $this->api->getPlaylistTracks($playlistID,array('offset'=>0));
        $uriArray['tracks'] = array();
        foreach($items->items as $item) {
            $uriArray['tracks'][] = array('id'=>$item->track->id);
        }
        return $uriArray;
    }

    /**
     * @param $playlistID
     * @return mixed
     */
	protected function getAlbumIDByPlaylist($playlistID) {
		$items = $this->api->getPlaylistTracks($playlistID);
		return $items->items{$this->offset}->track->album->id;
	}
}