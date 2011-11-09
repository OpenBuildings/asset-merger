<?php 

/**
* Combines assets and merges them to single files in production
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Block extends Asset
{
	private $content = null;

	private $_last_modified = null;

	function __construct($type, $content, $processor = null)
	{
		$this->processor = $processor ? $processor : Kohana::$config->load("asset-merger.processor.$type");
		$this->content = $content;
		$this->file = "Asset Block";
		$this->type = $type;
	}

	public function compile($process = null)
	{
		$content = $this->content;

		if( $process AND $this->processor)
		{
			$content = Asset_Processor::process($this->processor, $this->content);
		}

		return $content;
	}

	public function render($process = null)
	{
		switch($this->type)
		{
			case Assets::JAVASCRIPT:
				return '<script type="text/javascript">'.$this->compile($process)."</script>";

			case Assets::STYLESHEET:
				return '<style media="all">'.$this->compile()."</style>";
		}		
	}

	public function last_modified()
	{
		return null;
	}

	public function needs_recompile()
	{
		return false;
	}
}