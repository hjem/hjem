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

	public static function setActuator($targetType, $value) {
		foreach (self::$providers as $providerName) {
			$provider = new $providerName;

			foreach ($provider->providesActuators() as $name => $type) {
				if (strcasecmp($type, $targetType) !== 0) {
					continue;
				}

				$functionName = self::getFunctionName($name, $type, 'set');

				$provider->{$functionName}($value);

				return;
			}
		}

		throw new NoMatchException('No active provider can handle this action');
	}

	private static function simpleProviderName($providerName) {
		$parts = explode('\\', $providerName);
		return end($parts);
	}

	private static function convertTypeName($type) {
		$type = ucwords(strtolower($type));

		$type = preg_replace_callback('/_([a-z]?)/', function($match) {
            return strtoupper($match[1]);
        }, $type);

        return $type;
	}

	private static function humanReadableName($name, $type) {
		$name = Str::studly($name);
		$type = self::convertTypeName($type);

		$result = $name;

		if ($name != substr($type, 0, strlen($name))) {
			$result .= ' ' . $type;
		} else {
			$result = $type;
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

	private static function getFunctionName($name, $type, $prefix = 'get') {

		$functionName = str_replace(' ', '', self::humanReadableName($name, $type));

		return $prefix . $functionName;
	}

}