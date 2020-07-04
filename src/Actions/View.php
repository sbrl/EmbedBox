<?php

namespace EmbedBox\Actions;

use \SBRL\HttpResponse;
use \EmbedBox\CachedHighlighter;
use \EmbedBox\AccessController;

class View
{
	private $settings;
	private $highlighter;
	private $access_controller;
	
	function __construct(TomlConfig $settings, CachedHighlighter $highlighter, AccessController $access_controller) {
		$this->settings = $settings;
		$this->highlighter = $highlighter;
		$this->access_controller = $access_controller;
	}
	
	public function handle() : HttpResponse {
		if(!isset($_GET["url"]))
			return HttpResponse::create_error(400, "Error: The url GET parameter was not defined.");
		
		$url = $_GET["url"];
		$type = $_GET["type"] ?? "__AUTO__";
		
		if(!$this->access_controller->is_allowed($url))
			return HttpResponse::create_error(400, "Error: The url specified is not allowed by the defined access rules (if this is a mistake, update them in the settings file)");
		
		$result = $this->highlighter->highlight_url($url, $type);
		
		if(!$result->success)
			return HttpResponse::create_error(503, "Error: Something went wrong when highlighting the specified URL. Details: $result->error");
		
		return HttpResponse::create_nightink_file(200, "embed", (object) [
			"code" => $result->content,
			"url" => $url,
			"filename" => basename($url),
			"filesize" => $result->filesize,
			"language" => $result->type
		])->header_set("x-embedbox-cache", $result->was_hit ? "hit" : "miss");
	}
}
