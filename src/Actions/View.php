<?php

namespace EmbedBox\Actions;

use \SBRL\HttpResponse;

class View
{
	private $settings;
	
	function __construct(TomlConfig $settings) {
		$this->settings = $settings;
	}
	
	public function handle() : HttpResponse {
		
		return HttpResponse::create_file()
	}
}
