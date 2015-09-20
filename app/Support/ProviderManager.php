<?php

namespace App\Support;

use Illuminate\Support\Str;
use App\Activity;

class ProviderManager {

	public static $providers = [];

	public static function init() {
		if (count(self::$providers) <= 0) {
			self::$providers = [
				new Providers\NestThermostatProvider,
				new Providers\PhilipsHueProvider,
				new Providers\SonosProvider,
			];
		}
	}

	public static function pull() {
		foreach (self::$providers as $provider) {
			self::pullDataForProvider($provider);
		}
	}

	public static function getDataForQuery($targetType, $targetName = null) {
		foreach (self::$providers as $provider) {

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
					'provider' => self::simpleProviderName(get_class($provider)),
					'name' => self::humanReadableName($name, $type),
					'type' => $type,
					'value' => $val
				];
			}
		}

		throw new NoMatchException('No active provider has any data on this');
	}

	public static function setActuator($targetType, $value, $options = null) {
		foreach (self::$providers as $provider) {
			foreach ($provider->providesActuators() as $name => $type) {
				if (strcasecmp($type, $targetType) !== 0) {
					continue;
				}

				$functionName = self::getFunctionName($name, $type, 'set');

				return $provider->{$functionName}($value, $options);
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

	private static function pullDataForProvider($provider) {
		foreach ($provider->providesSensors() as $name => $type) {

			$functionName = self::getFunctionName($name, $type);

			$val = $provider->{$functionName}();

			$activity = new Activity;
			$activity->provider = get_class($provider);
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