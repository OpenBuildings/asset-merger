<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Collection of assets
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Kohana_Asset_Engine {

	/**
	 * Process asset with an engine
	 *
	 * @param   string  $engine
	 * @param   string  $content
	 * @param   string  $asset
	 * @return  mixed
	 */
	static public function process($engine, $content, $asset)
	{
		// Set method to call
		$method_call = array('Asset_Engine_'.ucfirst($engine), 'process');

		if ( ! class_exists($method_call[0]))
		{
			// No such engine
			throw new Kohana_Exception('The asset engine :engine does not exist', array(':engine' => $engine));
		}

		return call_user_func($method_call, $content, $asset);
	}

} // End Asset_Engine