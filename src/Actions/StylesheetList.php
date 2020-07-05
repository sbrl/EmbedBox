<?php

namespace EmbedBox\Actions;

use \SBRL\TomlConfig;
use \SBRL\HttpResponse;

class StylesheetList
{
	private $settings;
	
	function __construct(TomlConfig $settings) {
		$this->settings = $settings;
	}
	
	public function handle() : HttpResponse {
		$format = $_GET["format"] ?? "text";
		
		$themes = \HighlightUtilities\getAvailableStyleSheets();
		sort($themes);
		
		$mime = "text/plain";
		$result = null;
		switch ($format) {
			case "json":
				$mime = "application/json";
				$result = json_encode($themes);
				break;
			case "text":
				$result = implode("\n", $themes);
				break;
			
			default:
				return HttpResponse::create_error(400, "Error: Unknwon format $format (available formats for this action: text, json - default: text)");
				break;
		}
		
		return HttpResponse::create_simple(200, $result)
			->content_type($mime);
	}
}
