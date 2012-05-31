<?php defined('SYSPATH') or die('No direct script access.');
/**
* cssmin processor
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor_Cssmin {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		// Include the processor
		include_once Kohana::find_file('vendor', 'cssmin/cssmin-v1.0.1.b3');

		return cssmin::minify($content);
	}

} // End Asset_Processor_Cssmin