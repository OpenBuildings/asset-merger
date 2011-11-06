<?php

/**
* Lessphp engine
*/
class Asset_Processor_Cssmin
{
	static public function process($content)
	{
		include_once Kohana::find_file('vendor', 'cssmin/cssmin-v1.0.1.b3');
		
		return cssmin::minify($content);
	}	
}