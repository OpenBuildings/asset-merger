<?php

/**
* Lessphp engine
*/
class Asset_Processor_Csscompressor
{
	static public function process($content)
	{
		include_once Kohana::find_file('vendor', 'minify_css_compressor/Compressor');
		
		return minify_css_compressor::process($content);
	}	
}