<?php

/**
* Lessphp engine
*/
class Asset_Processor_Jsminplus
{
	static public function process($content)
	{
		include_once Kohana::find_file('vendor', 'jsminplus/jsminplus');
		
		return jsminplus::minify($content);
	}	
}