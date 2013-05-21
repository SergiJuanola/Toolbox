<?php 
/**
 * Tool for Toolbox
 * @package Toolbox
 */

require_once 'class.builder.php';

/**
* Connector to the Weather Channel API
*
* @package Toolbox
* @author 	Sergi Juanola 
* @copyright	Sergi Juanola 2012-2013
* @link Builder
*/
class Weather extends Builder {
	public static $default = array(
		'api'=>"",
		'format'=>"json",
		'info'=>NULL,
		'degrees'=>'c',
	);

	public static function build($config = array()) {
		$item = new self($config);
		if(!in_array($item->degrees, array('c', 'f')))
			$item->degrees = Weather::$default['degrees'];
		return $item;
	}

	public function forecast($countryState, $city)
	{
		$json_string = file_get_contents("http://api.wunderground.com/api/".$this->api."/geolookup/conditions/q/$countryState/$city.".$this->format);
		$parsed_json = json_decode($json_string, true);
		$this->info = $parsed_json;
		return $this;
	}

	public function getInfo()
	{
		if(empty($this->info))
			return NULL;
		return $this->info;
	}

	public function getCity()
	{
		if(empty($this->info))
			return NULL;
		return $this->info['location']['city'];
	}

	public function getTemperature()
	{
		if(empty($this->info))
			return NULL;
		return $this->info['current_observation']['temp_'.$this->degrees];
	}

	public function getWeathercode()
	{
		if(empty($this->info))
			return NULL;
		return $this->info['current_observation']['icon'];
	}
}