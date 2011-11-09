<?php

/**
* jsmin processor
*
* @package    OpenBuildings/asset-merger
* @author     Ivan K
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor_Jsmin
{
	static public function process($content)
	{
		include_once Kohana::find_file('vendor', 'jsmin/jsmin-1.1.1');
		
		return jsmin::minify($content);
	}	
}