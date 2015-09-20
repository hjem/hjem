<?php

use App\Support\ProviderManager;
use App\Support\ActuatorTypes;

class ActuatorApiTest extends TestCase
{
	public function testSetTargetTemperature() {
		$mockProvider = \Mockery::mock(App\Support\Providers\NestThermostatProvider::class)
			->shouldReceive('providesActuators')
			->andReturn(['Target' => ActuatorTypes::TARGET_TEMPERATURE])
			->shouldReceive('setTargetTemperature')
			->with(72, [])
			->mock();

		ProviderManager::$providers = [
			$mockProvider
		];

		$this->post('/v1/set/target_temperature/72')
			->seeJson([
				'success' => true,
			]);
	}

	public function testSetLightsOn() {
		$mockProvider = \Mockery::mock(App\Support\Providers\PhilipsHueProvider::class)
			->shouldReceive('providesActuators')
			->andReturn(['Lights' => ActuatorTypes::LIGHTS])
			->shouldReceive('setLights')
			->with('on', [])
			->mock();

		ProviderManager::$providers = [
			$mockProvider
		];

		$this->post('/v1/set/lights/on')
			->seeJson([
				'success' => true,
			]);
	}
}
