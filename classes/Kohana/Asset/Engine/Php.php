<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Pure php engine
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Kohana_Asset_Engine_Php {

	/**
	 * Process asset content
	 *
	 * @param   string  $content
	 * @param   Asset   $asset
	 * @return  string
	 */
	static public function process($content, Asset $asset)
	{
		// Turn on output buffering
		ob_start();

		// Eval
		eval('?>'.$content.'<?php ');

		return ob_get_clean();
	}

} // End Asset_Engine_Php