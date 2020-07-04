<?php

namespace EmbedBox;

use \Highlight\Highlighter;

class CachedHighlighter {
	private $settings;
	private $cache;
	
	function __construct(TomlSettings $settings, Stash\Pool $cache, Highlighter $highlighter) {
		$this->settings = $settings;
		$this->cache = $cache;
	}
	
	public function highlight_url($url, $do_cache = true) {
		$cache_key = hash("sha256", $url);
		
		// 1: Check the cache
		$item = $this->cache->getItem("render_url/$cache_key");
		if($item->isHit() && $do_cache)
			return (object) [ "result" => $item->get(), "was_hit" => true ];
		
		// 2: Render if it's not in the cache
		$item->lock(); // Let others know we're updating the cache
		$result = $this->do_highlight_string(
			
			file_get_contents($url)
		);
		
		// 3: Update the cache
		$this->cache->save($item->set($result));
		
		// 4: Return result
		return (object) [ "result" => $result, "was_hit" => false ];
	}
	
	private function do_highlight_string($type, $source) {
		
	}
}
