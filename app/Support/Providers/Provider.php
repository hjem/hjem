<?php

namespace App\Support\Providers;

abstract class Provider {

	abstract function providesSensors();
	abstract function providesActuators();

}