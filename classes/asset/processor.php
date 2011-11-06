<?php 

/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan K
* @copyright  (c) 2011 OpenBuildings
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Processor
{
	static public function process($processor, $content)
	{
		$method_call = array("Asset_Processor_".ucfirst($processor), "process");

		if( ! class_exists($method_call[0]))
			throw new Kohana_Exception(" The asset processor :processor does not exist", array(":processor" => $processor));

		return call_user_func($method_call, $content);
	}
}