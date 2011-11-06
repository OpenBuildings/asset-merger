<?php

/**
* cssmin processor
*
* @package    OpenBuildings/asset-merger
* @author     Ivan K
* @copyright  (c) 2011 OpenBuildings
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor_Cssmin
{
	static public function process($content)
	{
		include_once Kohana::find_file('vendor', 'cssmin/cssmin-v1.0.1.b3');
		
		return cssmin::minify($content);
	}	
}