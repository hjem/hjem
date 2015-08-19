<?php

use \Mockery as m;
use App\Support\Providers\NestThermostatProvider;

class NestThermostatProviderTest extends ProviderTest
{
    private $mockData = [
        'current_state' => [
            'mode' => 'cool',
            'temperature' => 73.346,
            'humidity' => 42,
            'ac' => 1,
            'heat' => false,
            'alt_heat' => false,
            'fan' => 1,
            'auto_away' => 0,
            'manual_away' => false,
            'leaf' => false,
            'battery_level' => 3.939,
        ],
        'target' => [
            'mode' => 'cool',
            'temperature' => 71.123,
            'time_to_target' => 1439955064,
        ],
        'serial_number' => 'XXXXXXXX',
        'scale' => 'F',
        'location' => 'XXXXXXXX',
        'network' => [
            'online' => 1,
            'last_connection' => '2015-08-19 00:47:25',
            'wan_ip' => '100.90.80.70',
            'local_ip' => '10.0.0.1',
            'mac_address' => 'XXXXXX',
        ],
        'name' => 'Not Set',
        'where' => 'Kitchen',
    ];

    private function getNestMock() {
        $nest = m::mock('Nest');
        $nest->shouldReceive('getDeviceInfo')
            ->andReturn($this->mockData);

        return $nest;
    }

    private function getProvider($nestMock = null) {
        if ($nestMock === null) {
            $nestMock = $this->getNestMock();
        }

        $provider = new NestThermostatProvider();
        $provider->setNest($nestMock);

        return $provider;
    }

    public function testGetIndoorTemperature() {
        $this->assertEquals(73.346, $this->getProvider()->getIndoorTemperature());
    }

    public function testGetTargetTemperature() {
        $this->assertEquals(71.123, $this->getProvider()->getTargetTemperature());
    }

    public function testGetHumidity() {
        $this->assertEquals(42, $this->getProvider()->getHumidity());
    }

    public function testGetAway() {
        $this->assertEquals(false, $this->getProvider()->getAway());
    }

    public function testSetTargetTemperature() {
        $targetTemperature = 40.0;

        $nestMock = $this->getNestMock();
        $nestMock->shouldReceive('setTargetTemperatureMode')
            ->with('cool', $targetTemperature);

        $provider = $this->getProvider($nestMock);
        $provider->setTargetTemperature($targetTemperature);
    }

    public function testSetAway() {
        $nestMock = $this->getNestMock();
        $nestMock->shouldReceive('setAway')
            ->with(true);

        $provider = $this->getProvider($nestMock);
        $provider->setAway(true);
    }
}
