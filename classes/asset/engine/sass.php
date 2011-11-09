<?php

/**
* Sass css engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan K
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Sass
{
	static public function process($content, Asset $asset)
	{
		$old = error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
		include_once Kohana::find_file('vendor/PHamlP/sass', 'SassParser');

		$sass = new SassParser(array());
		$content = $sass->toCss($content, false);
		error_reporting($old);
		return $content;

	}	
}