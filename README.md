<img src="http://i.imgur.com/gMhidJO.png" align="left">
<h1>hjem <em>&mdash; a self-hosted API for your home</em></h1>

> Note: "hjem" is a work in progress

## Summary
"hjem" acts as a switchboard for all your smart home devices such as the Nest Thermostat, Philips Hue, Sonos Speakers or your Alarm.com alarm system. The individual products are abstracted away and simple concepts like "temperature", "light", or "alarm" are used instead. This allows you to plug and play any components you'd like.

## Features
The app has three main purposes:
* **A central API to interact with and access all your devices:** Build your own custom apps, with one central API to control and consume data from your smart devices.
* **Collect & visualize data:** Monitor your electricy usage, temperature, etc.
* **Triggers and timers:** Flash lightbulbs when the washer is ready, fire up some music on your Sonos Speakers to wake you up in the morning, etc.

## Installation
You can install "hjem" like any other composer-based PHP app, in addition please make sure to create the `storage/database.sqlite` file for the sqlite database.

```bash
git clone git@github.com:hjem/hjem.git
cd hjem
composer install
touch storage/database.sqlite
```

## API

> The API is a work in progress and *will* change slightly as additional sensors and actuator types are added.

### Retrieve data (sensors)

**GET** `/v1/current/{SENSOR_TYPE}/{NAME}`

Currently available sensor types:
* Temperature
* Humidity
* Away

The `NAME` parameter is optional. If none is specified the default value for the sensor is returned (e.g. indoor temperature if you specify *temperature* as the sensor type).

#### Examples
* `/v1/current/temperature` &mdash; Returns current indoor temperature
* `/v1/current/temperature/target` &mdash; Returns target temperature
* `/v1/current/humidity` &mdash; Returns the current humidity
* `/v1/current/away` &mdash; Returns whether someone is home or not *(yet another reason not to make your API public :))*

### Perform action (actuators)

**POST** `/v1/set/{ACTUATOR_TYPE}/{VALUE}`

Currently available actuator types:
* Target Temperature
* Away
* Light On
* Light Off
* Light Color
* Music Pause

#### Examples
* `/v1/set/target_temperature/72` &mdash; Sets the target temperature to 72 degrees
* `/v1/set/away/on` &mdash; Sets status as being away
* `/v1/set/away/off` &mdash; Sets status as being at home
* `/v1/set/music/pause` &mdash; Pauses the music
* `/v1/set/music/resume` &mdash; Resumes the music
* `/v1/set/lights/on` &mdash; Turns on the lights
* `/v1/set/lights/off` &mdash; Turns off the lights
* `/v1/set/lights/color` &mdash; Changes lights to a specific color

## Providers

### Current
* Nest Learning Thermostat
* Philips Hue
* Alarm.com

### Planned
* Weather (Temperature/Rain/Wind)
* Washington Gas usage
* Dominion &mdash; Electricy usage
* Custom sensors (E.g. my homemade washer/dryer cycle sensor)
