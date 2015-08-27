<?php

namespace App\Support\Providers;

use App\Support\ActuatorTypes;
use App\Support\NoMatchException;

use duncan3dc\Sonos\Network;

class SonosSpotifyProvider extends Provider {

	private $client;

	public function __construct() {
		$this->client = new Network(new \Doctrine\Common\Cache\ArrayCache);
	}

	public function providesSensors() {
		return [];
	}

	public function providesActuators() {
		return [
			'PlayTrack' => ActuatorTypes::PLAY_TRACK,
			'PlayArtist' => ActuatorTypes::PLAY_ARTIST,
			'PlayAlbum' => ActuatorTypes::PLAY_ALBUM,
		];
	}

	public function setClient($client) {
		$this->client = $client;
	}

	public function setPlayTrack($val) {
		$val = urldecode($val);

		$trackMetadata = $this->searchForTrack($val);

		if (!$trackMetadata) {
			throw new NoMatchException('Could not find track matching your query: ' . $val);
		}
		
		$track = new \duncan3dc\Sonos\Tracks\Track('x-sonos-spotify:' . urlencode($trackMetadata->href) . '?sid=12&flags=8224&sn=1');
		$this->playSonosTrack($track);

		return $trackMetadata->name . ' by ' . $trackMetadata->artists[0]->name;
	}

	public function setPlayArtist($val) {
		
	}

	public function setPlayAlbum($val) {
		
	}

	public function playSonosTrack($track) {
		$controller = $this->client->getController();

		if (!$controller->isUsingQueue()) {
			$controller->useQueue();
		}

		$queue = $controller->getQueue();

		$queue->addTrack($track);
		$controller->selectTrack($queue->count() - 1);
		$controller->play();
	}

	private function searchForTrack($query) {
		$url = 'http://ws.spotify.com/search/1/track.json?q=' . urlencode($query);
		$json = json_decode(file_get_contents($url));

		foreach ($json->tracks as $track) {
			$availableTerritories = explode(' ', $track->album->availability->territories);

			if (in_array($this->currentTerritory(), $availableTerritories)) {
				return $track;
			}
		}

		return null;
	}

	// TODO: Make configurable
	private function currentTerritory() {
		return 'US';
	}
	
}