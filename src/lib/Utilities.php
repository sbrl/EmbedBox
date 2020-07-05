<?php

namespace SBRL;

/**
 * 
 */
class Utilities
{
	private function __construct() {  }
	
	public static function delete_recursive($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? delete_recursive("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	} 
}
