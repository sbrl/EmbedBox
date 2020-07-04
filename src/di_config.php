<?php

// Ref https://github.com/ConnectedHumber/Air-Quality-Web/blob/master/di_config.php

use Psr\Container\ContainerInterface;

use SBRL\PerformanceCounter;
use SBRL\TomlConfig;

// ----------------------------------------------------------------------------

return [
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
		$cache = new Stash\Pool(new Stash\Driver\FileSystem([
			"path" => ROOT_DIR."/data/cache/"
		]));
		return $cache;
	}
];
