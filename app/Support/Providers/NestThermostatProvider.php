<?php

namespace App\Support\Providers;

use App\Support\SensorTypes;
use App\Support\ActuatorTypes;
use Nest;

if (!class_exists('Nest')) {
	// Quick work-around since this library doesn't have an autoloader set up with composer \o/
	require(__DIR__ . '/../../../vendor/gboudreau/nest-api/nest.class.php');
}

class NestThermostatProvider extends Provider {

	private $nest;

	public function __construct() {
		$this->nest = new Nest(config('providers.nest_thermostat.email'), config('providers.nest_thermostat.password'));
	}

	public function providesSensors() {
		return [
			'Indoor' => SensorTypes::TEMPERATURE,
			'Target' => SensorTypes::TEMPERATURE,
			'Humidity' => SensorTypes::HUMIDITY,
			'Away' => SensorTypes::AWAY
		];
	}

	public function providesActuators() {
		return [
			'Target' => ActuatorTypes::TARGET_TEMPERATURE,
			'Away' => ActuatorTypes::AWAY
		];
	}

	public function setNest(Nest $nest) {
		$this->nest = $nest;
	}

	private function getInfo($key = null) {
		$info = json_decode(json_encode($this->nest->getDeviceInfo()), true); // Convert object to array
		$info = array_dot($info);

		if ($key) {
			return isset($info[$key]) ? $info[$key] : null;
		}

		return $info;
	}

	public function getIndoorTemperature() {
		return $this->getInfo('current_state.temperature');
	}

	public function getTargetTemperature() {
		return implode('-', (array)$this->getInfo('target.temperature')); // May be temperature range
	}

	public function getHumidity() {
		return $this->getInfo('current_state.humidity');
	}

	public function getAway() {
		return $this->getInfo('current_state.auto_away') == 1 || $this->getInfo('current_state.manual_away');
	}

	public function setTargetTemperature($targetTemperature, $options = null) {
		$existingTargetTemperatureType = $this->getInfo('target.mode');

		$this->nest->setTargetTemperatureMode($existingTargetTemperatureType, $targetTemperature);
	}

	public function setAway($away) {
		$this->nest->setAway($away);
	}
	
}