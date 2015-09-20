<?php

namespace App\Support\Providers;

use App\Support\ActuatorTypes;
use App\Support\NoMatchException;

class FrontpointSecurityProvider extends Provider {

	private $client;

	public function __construct() {
		
	}

	public function providesSensors() {
		return [];
	}

	public function providesActuators() {
		return [];
	}
	
}