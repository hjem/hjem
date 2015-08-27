<?php

namespace App\Support;

abstract class ActuatorTypes extends BasicEnum {

	const TARGET_TEMPERATURE = 'TARGET_TEMPERATURE';
	const AWAY = 'AWAY';
	const LIGHT_ON = 'LIGHT_ON';
	const LIGHT_OFF = 'LIGHT_OFF';
	const LIGHT_COLOR = 'LIGHT_COLOR';
	const PLAY_TRACK = 'PLAY_TRACK';
	const PLAY_ARTIST = 'PLAY_ARTIST';
	const PLAY_ALBUM = 'PLAY_ALBUM';

}