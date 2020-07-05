<?php

// Ref https://github.com/ConnectedHumber/Air-Quality-Web/blob/master/di_config.php

use Psr\Container\ContainerInterface;

use SBRL\PerformanceCounter;
use SBRL\TomlConfig;

// ----------------------------------------------------------------------------

return [
	"cache_dir_app" => ROOT_DIR."/data/cache/app",
	// These are created during initalisation, but we want them available via dependency injection too
	TomlConfig::class => function(ContainerInterface $c) {
		global $settings;
		return $settings;
	},
	PerformanceCounter::class => function(ContainerInterface $c) {
		global $perfcounter;
		return $perfcounter;
	},
	
	Stash\Pool::class => function(ContainerInterface $c) {
		$settings = $c->get(TomlConfig::class);
		$cache_dir = $c->get("cache_dir_app");
		// Create the cache directory if it doesn't exist already
		if(!file_exists($cache_dir))
			mkdir($cache_dir, 0700, true);
		
		$cache = new Stash\Pool(new Stash\Driver\FileSystem([
			"path" => $cache_dir
		]));
		return $cache;
	}
];
