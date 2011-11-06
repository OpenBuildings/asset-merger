<?php

/**
* Pure php engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan K
* @copyright  (c) 2011 OpenBuildings
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Php
{
	static public function process($content, Asset $asset)
	{
		ob_start();
		eval("?>" . $content . "<?php ");
		return ob_get_clean();
	}	
}