<?php 

/**
* Collection of assets
*/
class Asset_Processor
{
	static public function process($processor, $content)
	{
		$method_call = array("Asset_Processor", "process_$processor");

		if( ! method_exists($method_call[0], $method_call[1]))
			throw new Kohana_Exception(" The asset processor :processor does not exist", array(":processor" => $processor));

		return call_user_func($method_call, $content);
	}

	static public function process_cssmin( $content )
	{
		include_once Kohana::find_file('vendor', 'cssmin/cssmin-v1.0.1.b3');
		
		return cssmin::minify($content);
	}

	static public function process_css_compressor($content)
	{
		include_once Kohana::find_file('vendor', 'minify_css_compressor/Compressor');
		
		return minify_css_compressor::process($content);
	}

	static public function process_css_jsmin($content)
	{
		include_once Kohana::find_file('vendor', 'jsmin/jsmin-1.1.1');
		
		return jsmin::minify($content);
	}

	static public function process_css_jsminplus($content)
	{
		include_once Kohana::find_file('vendor', 'jsminplus/jsminplus');
		
		return jsminplus::minify($content);
	}	

}