<?php

namespace SBRL;

class DisplayFormatter
{
	/**
	 * Converts a filesize into a human-readable string.
	 * @package core
	 * @see	http://php.net/manual/en/function.filesize.php#106569	The original source
	 * @author	rommel
	 * @author	Edited by Starbeamrainbowlabs
	 * @param	int		$bytes		The number of bytes to convert.
	 * @param	int		$decimals	The number of decimal places to preserve.
	 * @return 	string				A human-readable filesize.
	 */
	static function human_filesize($bytes, $decimals = 2)
	{
		$sz = ["b", "kib", "mib", "gib", "tib", "pib", "eib", "yib", "zib"];
		$factor = floor((strlen($bytes) - 1) / 3);
		$result = round($bytes / pow(1024, $factor), $decimals);
		return $result . @$sz[$factor];
	}
}
