<?php 

namespace SBRL;

use \stdClass;
use \Yosymfony\Toml\Toml;
use \Yosymfony\Toml\TomlBuilder;

/**
 * Handles the loading and saving of settings from a toml file.
 * Supports loading default settings from a separate file.
 * @author			Starbeamrainbowlabs
 * @version			v0.5.2
 * @lastModified	22nd March 2017
 * @license			https://www.mozilla.org/en-US/MPL/2.0/	Mozilla Public License 2.0
 * Changelog:
 	 * v0.6 - 4th July 2020
	 	 * Add new has() method
	 	 * Add type hints
	 * v0.5.2 - 15th January 2018
		 * chmod auto-generated settings file to be 0600
	 * v0.5.1 - 14th January 2018
		 * Make auto-generated settings files valid
 	 * v0.5 - 9th September 2018
 	 	 * Made hasPropertyByPath() more intelligent
 	 * v0.4 - 23rd August 2018
 	 	 * Made getPropertyByPath() more intelligent
	 * v0.3 - 5th March 2018
		 * Forked to create a version for toml.
	 * v0.2 - 22nd March 2017
		 * Add license
		 * Filled in some missing comments
	 * v0.1: 28th February 2017
		 * Initial release
 */
class TomlConfig
{
	private $defaultSettings;
	
	private $settingsFilePath = "";
	/**
	 * The custom settings object.
	 * @var stdClass
	 */
	private $customSettings;
	
	/**
	 * The banner to insert at the top of the custom settings file when saving it.
	 * @var string
	 */
	private $customSettingsBanner;
	
	/**
	 * Creates a new JsonConfig
	 * @param string $settingsFilePath			The path to the customised settings file.
	 * @param string $defaultSettingsFilePath	The path to the default settings file.
	 */
	public function __construct(string $settingsFilePath, string $defaultSettingsFilePath) {
		$this->customSettingsBanner = "# -------[ Custom Settings File - Last updated " . date("Y-m-d") . " ]-------\n[http]\nroot_url = \"CHANGE_ME\"\n#root_url = \"https://example.com/path/to/index.php\"\n\n[access_control]\nallow = [\n\t# Enter regexes as strings here\n\t#\"^https://((raw|gist)\.)?git(hub|lab)\.com/.*$\"\n]\n";
		
		$this->defaultSettings = Toml::ParseFile($defaultSettingsFilePath, true);
		
		$this->customSettings = new stdClass();
		if(file_exists($settingsFilePath)) {
			$this->customSettings = Toml::ParseFile($settingsFilePath, true);
		} else {
			mkdir(dirname($settingsFilePath), 0750, true);
			file_put_contents($settingsFilePath, "$this->customSettingsBanner\n");
			chmod($settingsFilePath, 0600);
		}
	}
	
	/**
	 * Determines whether the given settings exists or not (in either
	 * the custom *or* the default settings).
	 * @param  string $key The key of the setting to lookup.
	 * @return bool        Whether a setting with the specified key exists or not.
	 */
	public function has(string $key) : bool {
		return static::hasPropertyByPath($this->customSettings, $key)
			|| static::hasPropertyByPath($this->defaultSettings, $key);
	}
	
	/**
	 * Gets the value identified by the specified property, in dotted notation (e.g. `Network.Firewalling.Ports.Http`)
	 * @param	string	$key	The property, in dotted notation, to return.
	 * @return	mixed			The value odentified by the given dotted property notation.
	 */
	public function get(string $key) {
		if(static::hasPropertyByPath($this->customSettings, $key)) {
			return static::getPropertyByPath($this->customSettings, $key);
		}
		return static::getPropertyByPath($this->defaultSettings, $key);
	}
	/**
	 * Fetches the default value for the specified property, specified in dotted notation.
	 * @param  string	$key	The key to fetch.
	 * @return mixed	The default value of the property identified by the given dotted notation.
	 */
	public function getDefault(string $key) {
		return static::getPropertyByPath($this->defaultSettings, $key);
	}
	/**
	 * Determines whether the given property has been customised or explicitly specified in the customised settings file.
	 * @param	string	$key	The property, in dotted notation, to check.
	 * @return	boolean			Whether the specified properties file has been explicitly specified in the custom settings file.
	 */
	public function hasChanged(string $key) {
		return static::hasPropertyByPath($this->customSettings, $key);
	}
	/**
	 * Sets the property identified by the specified dotted path.
	 * Does not re-save the properties back to disk!
	 * @param	string	$key	The dotted path to update.
	 * @param	mixed	$value	The value to set the property to.
	 * @return	self			This ConfigFile instance, to aid method chaining.
	 */
	public function set(string $key, $value) {
		static::setPropertyByPath($this->customSettings, $key, $value);
		return $this;
	}
	
	public function setCustomSettingsBanner(string $value) {
		$this->customSettingsBanner = $value;
		return $this;
	}
	
	/**
	 * Saves the customised settings back to the disk.
	 * @return boolean Whether the save was successful or not.
	 */
	public function save() : bool {
		$output_builder = new TomlBuilder();
		$toml_builder->addComment($this->customSettingsBanner);
		
		$this->build($toml_builder, $this->customSettings);
		
		return file_put_contents($settingsFilePath, $toml_builder->getTomlString());
	}
	
	private function build(TomlBuilder $toml_builder, stdClass $customSettingsObject, $subobject_name = null) : void {
		if($subobject_name !== null)
			$toml_builder->addTable($subobject_name);
		
		foreach($customSettingsObject as $key => $value) {
			if(!\is_object($value))
				$toml_builder->addValue($key, $value);
			else
				$this->build($toml_builder, $value, "$subobject_name.$key");
		}
	}
	
	/**
	 * Works out whether a given dotted path to a property exists on a given object.
	 * @param	stdClass  	$obj	The object to search.
	 * @param	string		$path	The dotted path to search with. e.g. `Network.Firewall.OpenPorts`
	 * @return	boolean		Whether the given property is present in the given object.
	 */
	public static function hasPropertyByPath(stdClass $obj, string $path) : bool {
		$pathParts = explode(".", $path);
		$subObj = $obj;
		foreach($pathParts as $part) {
			if(is_object($subObj) && !isset($subObj->$part))
				return false;
			else if(is_array($subObj) && !isset($subObj[$part]))
				return false;
			else if(!is_object($subObj) && !is_array($subObj))
				return false;
			
			if(is_object($subObj))
				$subObj = $subObj->$part;
			else // It must be an array
				$subObj = $subObj[$part];
		}
		return true;
	}
	/**
	 * Returns the property identified by the given dotted path.
	 * If you're not sure whether a property exists or not, use static::hasPropertyByPath() first to check.
	 * @param	stdClass	$obj	The object to fetch from.
	 * @param	string		$path	The path to extract from the given object.
	 * @return	mixed				The property identified by the given object.
	 */
	public static function getPropertyByPath(stdClass $obj, string $path) {
		$pathParts = explode(".", $path);
		$subObj = $obj;
		foreach($pathParts as $part) {
			if(is_object($subObj))
				$subObj = $subObj->$part;
			else if(is_array($subObj))
				$subObj = $subObj[$part];
			else
				throw new Exception("Error: Can't find part '$part' on sub-item if it isn't an object or array");
		}
		return $subObj;
	}
	/**
	 * Returns the property identified by the given dotted path.
	 * If you're not sure whether a property exists or not, use static::hasPropertyByPath() first to check.
	 * @param	stdClass	$obj	The object to set the property on.
	 * @param	string		$path	The path to set on the given object.
	 * @param	mixed		$value	The value to  set the given property to.
	 */
	public static function setPropertyByPath(stdClass $obj, string $path, $value) : void {
		$pathParts = explode(".", $path);
		$pathPartsCount = count($pathParts);
		
		$subObj = $obj;
		for($i = 0; $i < $pathPartsCount; $i++) {
			// Set the property on the last iteration
			if($i + 1 == $pathPartsCount) {
				$subObj->$part = $value;
				break;
			}
			// Create sub-objects if they dobn't exist already
			if(!isset($subObj->$part)) {
				$subObj->$part = new stdClass();
			}
			$subObj = $subObj->$part;
		}
	}
}
