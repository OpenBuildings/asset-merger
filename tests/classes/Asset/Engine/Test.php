<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Test engine (noop)
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Test {

	static public function process($content, Asset $asset)
	{
		return $content.' CONVERTED ';
	}

} // End Asset_Engine_Less