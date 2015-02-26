<?php defined('SYSPATH') OR die('No direct script access.');
/**
* test processor (noop)
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor_Test {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @return  string
	 */
	static public function process($content)
	{
		return $content.' PROCESSED ';
	}

} // End Asset_Processor_Jsmin