<?php

namespace EmbedBox;

use \SBRL\TomlConfig;

/**
 * Determines whether URLs are allowed or not.
 */
class AccessController
{
	private $settings;
	
	function __construct(TomlConfig $settings) {
		$this->settings = $settings;
	}
	
	public function is_allowed(string $url) : bool {
		if($this->is_denied($url))
			return false;
		
		if($this->matches_regex_array($url, $this->get_allow_regexes()))
			return true;
		
		return false;
	}
	
	/**
	 * Checks to see if a given URL is explcitly denied or not.
	 * @param  string $url The url to check.
	 * @return bool        Whether the given URL is explicitly denied or not.
	 */
	private function is_denied(string $url) : bool {
		return $this->matches_regex_array($url, $this->get_deny_regexes());
	}
	
	private function matches_regex_array(string $subject, array $regexes) {
		foreach($regexes as $regex) {
			if(preg_match("/$regex/", $subject) == 1)
				return true;
		}
		return false;
	}
	
	// HACK: yosymfony/toml doesn't support empty arrays :-/
	
	private function get_allow_regexes() {
		if(!$this->settings->has("access_control.allow")) return [];
		return $this->settings->get("access_control.allow");
	}
	private function get_deny_regexes() {
		if(!$this->settings->has("access_control.deny")) return [];
		return $this->settings->get("access_control.deny");
	}
}
