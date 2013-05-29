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
* @see  Builder
*/
class Weather extends Builder {
	/**
	* Default properties.
	* @param string $api The api user
	* @param string $format The format type. Defaults to "json"
	* @param array $info The data that's returned from the API
	* @param char $degrees The degrees used. 'c' for Celsius and 'f' for Farenheit
	*/
	public static $default = array(
		'api'=>"",
		'format'=>"json",
		'info'=>NULL,
		'degrees'=>'c',
	);

	/**
	* Building method
	* @param array $config The config array
	* @see  Builder::build()
	*/
	public static function build($config = array()) {
		$item = new self($config);
		if(!in_array($item->degrees, array('c', 'f')))
			$item->degrees = Weather::$default['degrees'];
		return $item;
	}


	/**
	* Return the forecast of a specific city
	* @param string $countryState The country or state the city is in
	* @param string $city The city to check the forecast
	*/
	public function forecast($countryState, $city)
	{
		$json_string = file_get_contents("http://api.wunderground.com/api/".$this->api."/geolookup/conditions/q/$countryState/$city.".$this->format);
		$parsed_json = json_decode($json_string, true);
		$this->info = $parsed_json;
		return $this;
	}


	/**
	* Get the current info retrieved by forecast()
	* @return array The retrieved information
	* @see  Weather::forecast()
	*/
	public function getInfo()
	{
		if(empty($this->info))
			return NULL;
		return $this->info;
	}


	/**
	* Get the city of the retrieved information
	* @return string The city name
	*/
	public function getCity()
	{
		if(empty($this->info))
			return NULL;
		return $this->info['location']['city'];
	}


	/**
	* Get the temperature of the retrieved information
	* @return string The temperature name
	*/
	public function getTemperature()
	{
		if(empty($this->info))
			return NULL;
		return $this->info['current_observation']['temp_'.$this->degrees];
	}


	/**
	* Get the weather code of the retrieved information
	* @return string The weather code name
	*/
	public function getWeathercode()
	{
		if(empty($this->info))
			return NULL;
		return $this->info['current_observation']['icon'];
	}
}