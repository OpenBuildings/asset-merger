<?php defined('SYSPATH') or die('No direct script access.');
/**
* Combines assets and merges them to single files in production
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Block extends Asset {

	/**
	 * @var  string  content
	 */
	private $content = NULL;

	/**
	 * @var  int  last modified time
	 */
	private $_last_modified = NULL;

	/**
	 * Set up environment
	 *
	 * @param  string $type
	 * @param  string $content
	 * @param  array  $options
	 */
	function __construct($type, $content, array $options = NULL)
	{
		// Set processor
		$this->processor = Arr::get($options, 'processor', Kohana::$config->load('asset-merger.processor.'.$type));

		// Set condition
		$this->condition = Arr::get($options, 'condition');
		
		// Set content, file and type
		$this->content = $content;
		$this->file    = 'Asset Block';
		$this->type    = $type;
	}

	/**
	 * Compile asset block
	 *
	 * @param   bool  $process
	 * @return  string
	 */
	public function compile($process = FALSE)
	{
		// Set content
		$content = $this->content;

		if ($process AND $this->processor)
		{
			// Process content
			$content = Asset_Processor::process($this->processor, $this->content);
		}

		return $content;
	}

	/**
	 * Render HTML
	 *
	 * @param   bool  $process
	 * @return  string
	 */
	public function render($process = NULL)
	{
		switch($this->type)
		{
			case Assets::JAVASCRIPT:
				$html = '<script type="text/javascript">'.$this->compile($process)."</script>";
			break;
			case Assets::STYLESHEET:
				$html = '<style media="all">'.$this->compile()."</style>";
			break;
		}

		return $html;
	}

	/**
	 * Force last modified to return NULL
	 *
	 * @return NULL
	 */
	public function last_modified()
	{
		return NULL;
	}

	/**
	 * Force no recompile
	 *
	 * @return bool
	 */
	public function needs_recompile()
	{
		return FALSE;
	}

} // End Asset_Block