<?php

namespace EmbedBox;

use \Highlight\Highlighter;

class CachedHighlighter {
	private $settings;
	private $cache;
	
	function __construct(TomlSettings $settings, Stash\Pool $cache, Highlighter $highlighter) {
		$this->settings = $settings;
		$this->cache = $cache;
		$this->highlighter = $highlighter;
	}
	
	public function highlight_url(string $url, string $type, $do_cache = true) {
		// 0: Setup and validation
		if($type == "__AUTO__")
			$type = $this->get_file_ext($url);
		if($type == null)
			return (object) [ "success" => false, "error" => "autodetect_failed" ];
		
		$cache_key = $this->get_cache_key($url, $type);
		
		
		// 1: Check the cache
		$item = $this->cache->getItem("render_url/$cache_key");
		if($item->isHit() && $do_cache) {
			$result = $item->get();
			return (object) [
				"success" => true,
				"content" => $result->content,
				"filesize" => $result->filesize,
				"type" => $type,
				"was_hit" => true
			];
		}
		
		
		// 2: Render if it's not in the cache
		$item->lock(); // Let others know we're updating the cache
		$content = file_get_contents($url);
		$result = $this->do_highlight_string(
			$type,
			$content
		);
		
		
		// 3: Update the cache
		$item->expiresAfter($this->settings->get("cache.lifetime"));
		$this->cache->save($item->set([
			"content" => $result,
			"filesize" => strlen($content)
		]));
		
		
		// 4: Return result
		return (object) [
			"success" => true,
			"content" => $result,
			"type" => $type,
			"was_hit" => false,
			"filesize" => strlen($content)
		];
	}
	
	private function get_cache_key(string $url, string $type) : string {
		return hash("sha256", 
			hash("sha256", $url)."|".hash("sha256", $type)
		);
	}
	
	private function get_file_ext(string $url) {
		$filename = basename($url);
		$pos = strpos($filename, ".");
		if($pos === false)
			return null;
		
		return substr($filename, $pos + 1);
	}
	
	private function do_highlight_string($type, $source) {
		$result = null;
		try {
			$result = $this->highlighter->highlight($type, $source);
		} catch(DomainException $error) {
			$result = (object) [
				"language" => "unknown-language",
				"value" => htmlentities($source)
			];
		}
		return "<pre><code class='hljs {$result->language}'>$result->value</code></pre>";
	}
}
