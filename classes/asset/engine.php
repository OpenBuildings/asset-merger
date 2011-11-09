<?php 

/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine
{
	static public function process($engine, $content, $asset)
	{
		$method_call = array("Asset_Engine_".ucfirst($engine), "process");

		if( ! class_exists($method_call[0]))
			throw new Kohana_Exception(" The asset engine :engine does not exist", array(":engine" => $engine));

		return call_user_func($method_call, $content, $asset);
	}
}