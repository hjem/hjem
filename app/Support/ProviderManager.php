<?php

namespace App\Support;

use Illuminate\Support\Str;

class ProviderManager {

	private static $providers = [
		Providers\NestThermostatProvider::class
	];

	public static function pull() {
		foreach (self::$providers as $providerName) {
			info($providerName);

			$provider = new $providerName;

			foreach ($provider->providesSensors() as $name => $type) {
				$val = $provider->{'get' . Str::studly($name)}();
				info($name . ' (' . $type . ')' . ': ' . $val);
			}
		}
	}

}