<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Collection of assets
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Kohana_Asset_Processor {

	/**
	 * Process asset
	 *
	 * @param   string  $processor
	 * @param   string  $content
	 * @return  mixed
	 */
	static public function process($processor, $content)
	{
		// Set method to call
		$method_call = array('Asset_Processor_'.ucfirst($processor), 'process');

		if ( ! class_exists($method_call[0]))
		{
			// No such processor
			throw new Kohana_Exception('The asset processor :processor does not exist', array(':processor' => $processor));
		}

		return call_user_func($method_call, $content);
	}

} // Asset_Processor