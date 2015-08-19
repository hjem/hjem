<?php

return [

    'nest_thermostat' => [
        'email' => env('NEST_THERMOSTAT_EMAIL'),
        'password' => env('NEST_THERMOSTAT_PASSWORD'),
    ],

    'philips_hue' => [
    	'hub_ip_address' => env('PHILIPS_HUE_HUB_IP_ADDRESS'),
    	'username' => env('PHILIPS_HUE_USERNAME'),
    ]

];
