<?php

namespace SBRL;

/**
 * Represents a HTTP message.
 */
class HttpResponse
{
	private $has_rendered = false;
	
	private $status = 200;
	private $headers = [];
	private $body = null;
	
	function __construct() {
		
	}
	
	public function status_set(int $status) : HttpResponse {
		$this->status = $status;
		return $this;
	}
	public function status_get() : int {
		return $this->status;
	}
	
	
	public function header_set(string $name, string $value) : HttpResponse {
		$this->headers[$name] = $value;
		return $this;
	}
	public function header_get(string $name) : string {
		if(!isset($this->headers[$name]))
			return null;
		return $this->headers[$name];
	}
	public function header_get_default(string $name, strign $default) : string {
		if(!isset($this->headers[$name]))
			return $default ;
		return $this->headers[$name];
	}
	
	public function content_type(string $type) : HttpResponse {
		$this->header_set("content-type", $type);
		return $this;
	}
	
	public function body_set($body) : HttpResponse {
		$this->body = $body;
		return $this;
	}
	public function body_get() {
		return $this->body;
	}
	
	// ------------------------------------------------------------------------
	
	public function render() {
		if($this->has_rendered)
			throw new Exception("Error: This HttpResponse has been rendered already.");
		
		http_response_code($this->status);
		foreach($this->headers as $name => $value) {
			header("$name: $value");
		}
		echo($this->body);
		
		$this->has_rendered = true;
	}
	
	// ------------------------------------------------------------------------
	
	public static function create_simple(int $status, $body) : HttpResponse {
		return static::create($status, [], $body);
	}
	public static function create(int $status, array $headers, $body) : HttpResponse {
		$result = (new static())
			->status_set($status)
			->body_set($body);
		foreach($headers as $name => $value) {
			$result->header_set($name, $value);
		}
		return $result;
	}
	
	public static function create_simple_nightink(int $status, string $template, $options) : HttpResponse {
		return static::create_nightink($status, [], $template, $options);
	}
	public static function create_nightink(int $status, array $headers, string $template, $options) : HttpResponse {
		$nightink = new NightInk();
		return static::create(
			$status,
			$headers,
			$nightink->render($template, $options)
		);
	}
	
	public static function create_nightink_file(int $status, array $headers, string $template_name, $options) : HttpResponse  {
		global $settings;
		
		$nightink = new NightInk();
		return static::create(
			$status,
			$headers,
			$nightink->render_file(
				ROOT_DIR . "/"
					. $settings->get("internal.templating.templates_path") . "/"
					. $template_name.".html",
				$options
			)
		);
	}
	public static function create_nightink_file_simple(int $status, string $template_name, $options) : HttpResponse {
		return static::create_nightink_file(
			$status,
			[],
			$template_name,
			$options
		);
	}
	
	public static function create_error(int $status, string $message) : HttpResponse {
		return static::create_simple($status, $message)
			->content_type("text/plain");
	}
}
