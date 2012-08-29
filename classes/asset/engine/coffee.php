<?php defined('SYSPATH') or die('No direct script access.');
/**
* Coffiescript engine
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Coffee {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @param   Asset   $asset
	 * @return  string
	 */
	static public function process($content, Asset $asset)
	{
		// Set error reporting
		$old = error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));

		// Include the engine
		include_once Kohana::find_file('vendor/coffeescript', 'coffeescript');

		// Set content
		$content = CoffeeScript\compile($content);

		// Set error reporting
		error_reporting($old);

		return $content;
	}

} // End Asset_Engine_Coffee