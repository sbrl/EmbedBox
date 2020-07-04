<?php

namespace EmbedBox\Actions;

use \SBRL\TomlConfig;
use \SBRL\HttpResponse;

class Stylesheet
{
	private $settings;
	
	function __construct(TomlConfig $settings) {
		$this->settings = $settings;
	}
	
	public function handle() : HttpResponse {
		$theme_name = $_GET["theme"] ?? $this->settings->get("highlighting.default_theme");
		$css = \HighlightUtilities\getStyleSheet($theme_name);
		
		if($css === false)
			return HttpResponse::create_error(404, "Error: No theme was found with the name '$theme_name' (try the stylesheet-list action)");
		
		return HttpResponse::create_simple(200, $css)
			->content_type("text/css");
	}
}
