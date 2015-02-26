<?php defined('SYSPATH') OR die('No direct script access.');
/**
* jsminplus engine
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Kohana_Asset_Processor_Jsminplus {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		// Include the processor
		include_once Kohana::find_file('vendor', 'jsminplus/jsminplus');
		
		return jsminplus::minify($content);
	}

} // End Asset_Processor_Jsminplus