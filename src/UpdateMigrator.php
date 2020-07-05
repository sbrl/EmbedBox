<?php

namespace EmbedBox;

class UpdateMigrator {
	private $filename_lastversion = ROOT_DIR."/data/last_version";
	private $filename_thisversion = ROOT_DIR."/version";
	
	function __construct() {
		
	}
	
	public function has_updated() : bool {
		if(!file_exists($this->filename_lastversion)) {
			$this->update_lastversion();
			return false;
		}
		
		$last_version = file_get_contents($this->filename_lastversion);
		$this_version = file_get_contents($this->filename_thisversion);
		
		if($last_version !== $this_version)
			return true;
		
		return false;
	}
	
	private function update_lastversion() : void {
		copy($this->filename_thisversion, $this->filename_lastversion);
	}
	
	public function check() : void {
		if(!$this->has_updated())
			return;
		
		// We've updated
		$this->do_update();
	}
	
	private function do_update() : void {
		error_log("[EmbedBox] Update detected, running migration tasks");
		$this->do_update_global();
		error_log("[EmbedBox] Update complete");
		
		$this->update_lastversion();
	}
	
	private function do_update_global() : void {
		// Delete the PHP-DI cache on update
		if(file_exists(ROOT_DIR."/data/cache/php_di"))
			\SBRL\Utilities::delete_recursive(ROOT_DIR."/data/cache/php_di");
		if(file_exists(ROOT_DIR."/data/cache/php_di_proxies"))
			\SBRL\Utilities::delete_recursive(ROOT_DIR."/data/cache/php_di_proxies");
	}
}
