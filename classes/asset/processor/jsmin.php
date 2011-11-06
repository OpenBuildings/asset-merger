<?php

/**
* Lessphp engine
*/
class Asset_Processor_Jsmin
{
	static public function process($content)
	{
		include_once Kohana::find_file('vendor', 'jsmin/jsmin-1.1.1');
		
		return jsmin::minify($content);
	}	
}