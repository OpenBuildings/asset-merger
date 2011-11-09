<?php

/**
* Coffiescript engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Coffee
{
	static public function process($content, Asset $asset)
	{
		$old = error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
		include_once Kohana::find_file('vendor/coffeescript', 'coffeescript');

		$content = CoffeeScript\compile($content);	
		error_reporting($old);
		return $content;
	}	
}