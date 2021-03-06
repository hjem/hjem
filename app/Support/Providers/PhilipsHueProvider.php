<?php

namespace App\Support\Providers;

use App\Support\ActuatorTypes;
use App\Support\NoMatchException;
use Phue\Client;

class PhilipsHueProvider extends Provider {

	private $client;

	public function __construct() {
		$this->client = new Client(
			config('providers.philips_hue.hub_ip_address'),
			config('providers.philips_hue.username')
		);
	}

	public function providesSensors() {
		return [];
	}

	public function providesActuators() {
		return [
			'Lights' => ActuatorTypes::LIGHTS
		];
	}

	public function setClient($client) {
		$this->client = $client;
	}

	private function getLight($lightId) {
		$lights = $this->client->getLights();

		if (!isset($lights[$lightId])) {
			throw new NoMatchException('Unknown light id ' . $lightId);
		}

		return $lights[$lightId];
	}

	public function setLights($action, $options = null) {
		if (!$options || !isset($options['id'])) {
			$lights = $this->client->getLights();
		} else {
			$lights = [$this->getLight($options['id'])];
		}

		switch ($action) {
			case 'on':
				foreach ($lights as $light) {
					$this->setLightOn($light, true);
				}
				break;

			case 'off':
				foreach ($lights as $light) {
					$this->setLightOn($light, false);
				}
				break;

			case 'color':
				foreach ($lights as $light) {
					$this->setLightOn($light, true);
					$this->setLightColor($light, $options['r'], $options['g'], $options['b']);
				}
				break;
			
			default:
				throw new InvalidArgumentException('Invalid light action: ' . $action);
				break;
		}
	}

	private function setLightOn($lightId, $shouldBeOn) {
		$light = $this->getLight($lightId);
		$light->setOn($shouldBeOn);
	}

	private function setLightColor($lightId, $r, $g, $b) {
		$light = $this->getLight($lightId);

		list($x, $y) = $this->convertRGBToCIE1931($r, $g, $b);
		$light->setXY($x, $y);
	}

	private function convertRGBToCIE1931($r, $g, $b) {
		$X = 0.4124 * $r + 0.3576 * $g + 0.1805 * $b;
		$Y = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

		$x = $X / ($X + $Y + $Z);
		$y = $Y / ($X + $Y + $Z);

		return [$x, $y];
	}
	
}