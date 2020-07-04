<?php

namespace EmbedBox\Actions;

use \SBRL\HttpResponse;
use \EmbedBox\CachedHighlighter;
use \EmbedBox\AccessController;

class View
{
	private $settings;
	
	function __construct(TomlConfig $settings) {
		$this->settings = $settings;
	}
	
	public function handle() : HttpResponse {
		if($this->settings->get("http.root_url") == "CHANGE_ME")
			return HttpResponse::create_error(503, "Error: The root url has not yet been defined. Do this in the settings file now (hint: it's the http.root_url setting).");
		return HttpResponse::create_nightink_file(200, "builder", (object) [
			"root_url" => $this->settings->get("http.root_url")
		]);
	}
}
