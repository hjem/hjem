<?php

namespace App\Support;

use Illuminate\Support\Str;
use App\Activity;

class ProviderManager {

	private static $providers = [
		Providers\NestThermostatProvider::class,
		Providers\PhilipsHueProvider::class,
	];

	public static function pull() {
		foreach (self::$providers as $providerName) {
			self::pullDataForProvider($providerName);
		}
	}

	public static function getDataForQuery($targetType, $targetName = null) {
		foreach (self::$providers as $providerName) {
			$provider = new $providerName;

			foreach ($provider->providesSensors() as $name => $type) {
				if ($targetName !== null && strcasecmp($name, $targetName) !== 0) {
					continue;
				}

				if (strcasecmp($type, $targetType) !== 0) {
					continue;
				}

				$functionName = self::getFunctionName($name, $type);

				$val = $provider->{$functionName}();

				return [
					'provider' => self::simpleProviderName($providerName),
					'name' => self::humanReadableName($name, $type),
					'type' => $type,
					'value' => $val
				];
			}
		}

		throw new NoMatchException('No active provider has any data on this');
	}

	private static function simpleProviderName($providerName) {
		$parts = explode('\\', $providerName);
		return end($parts);
	}

	private static function humanReadableName($name, $type) {
		$name = Str::studly($name);
		$type = ucwords(strtolower($type));

		$result = $name;

		if ($name != $type) {
			$result .= ' ' . $type;
		}

		return $result;
	}

	private static function pullDataForProvider($providerName) {
		$provider = new $providerName;

		foreach ($provider->providesSensors() as $name => $type) {

			$functionName = self::getFunctionName($name, $type);

			$val = $provider->{$functionName}();

			$activity = new Activity;
			$activity->provider = $providerName;
			$activity->name = $name;
			$activity->value = $val;
			$activity->save();
		}
	}

	private static function getFunctionName($name, $type) {

		$functionName = str_replace(' ', '', self::humanReadableName($name, $type));

		return 'get' . $functionName;
	}

}