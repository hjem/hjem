<?php

namespace App\Support;

use Illuminate\Support\Str;
use App\Activity;

class ProviderManager {

	private static $providers = [
		Providers\NestThermostatProvider::class
	];

	public static function pull() {
		foreach (self::$providers as $providerName) {
			self::pullDataForProvider($providerName);
		}
	}

	private static function pullDataForProvider($providerName) {
		$provider = new $providerName;

		foreach ($provider->providesSensors() as $name => $type) {
			$val = $provider->{'get' . Str::studly($name)}();

			$activity = new Activity;
			$activity->provider = $providerName;
			$activity->name = $name;
			$activity->value = $val;
			$activity->save();
		}
	}

}