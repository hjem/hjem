<?php

namespace App\Support\Providers;

use App\Support\ActuatorTypes;
use App\Support\NoMatchException;

use duncan3dc\Sonos\Network;

use InvalidArgumentException;

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

	public function setSpeaker($action, $options = null) {
		$controller = $this->client->getController();

		switch ($action) {
			case 'pause':
				$controller->pause();
				break;

			case 'play':
				$controller->play();
				break;
			
			default:
				throw new InvalidArgumentException('Invalid speaker action: ' . $action);
				break;
		}
	}
	
}