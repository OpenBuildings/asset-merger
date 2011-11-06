<?php

/**
* Pure php engine
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