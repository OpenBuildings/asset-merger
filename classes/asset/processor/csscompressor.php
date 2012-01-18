<?php defined('SYSPATH') or die('No direct script access.');
/**
* minify_css_compressor processor
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor_Csscompressor {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		// Include the processor
		include_once Kohana::find_file('vendor', 'minify_css_compressor/Compressor');
		
		return minify_css_compressor::process($content);
	}

} // End Asset_Processor_Csscompressor