<?php

/**
* Lessphp engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Less
{
	static public function process($content, Asset $asset)
	{
		include_once Kohana::find_file('vendor/lessphp', 'lessc');

		$lc = new lessc();
		$lc->importDir = dirname($asset->source_file()).DIRECTORY_SEPARATOR;
		return $lc->parse($content);		
	}	
}