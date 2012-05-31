<?php defined('SYSPATH') or die('No direct script access.');
/**
* jsmin processor
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor_Jsmin {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		// Include the processor
		include_once Kohana::find_file('vendor', 'jsmin/jsmin-1.1.1');
		
		return jsmin::minify($content);
	}

} // End Asset_Processor_Jsmin