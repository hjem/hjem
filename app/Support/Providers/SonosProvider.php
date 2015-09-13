<?php

namespace App\Support\Providers;

use App\Support\ActuatorTypes;
use App\Support\NoMatchException;

use duncan3dc\Sonos\Network;

class SonosProvider extends Provider {

	private $client;

	public function __construct() {
		$this->client = new Network(new \Doctrine\Common\Cache\ArrayCache);
	}

	public function providesSensors() {
		return [];
	}

	public function providesActuators() {
		return [
			'MusicPause' => ActuatorTypes::MUSIC_PAUSE
		];
	}

	public function setClient($client) {
		$this->client = $client;
	}

	public function setMusicPause($val) {
		$shouldPause = ($val == 'true');

		$controller = $this->client->getController();

		if ($shouldPause) {
			$controller->pause();
		} else {
			$controller->play();
		}
	}
	
}