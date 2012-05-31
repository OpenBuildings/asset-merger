<?php defined('SYSPATH') or die('No direct script access.');
/**
* Combines assets and merges them to single files in production
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset {

	/**
	 * @var  string  type
	 */
	public $type = NULL;

	/**
	 * @var  string  file
	 */
	public $file = NULL;
	
	/**
	 * @var  array  engines
	 */
	protected $engines = array();

	/**
	 * @var  array processors
	 */
	protected $processor = array();

	/**
	 * @var   string  source file
	 */
	protected $source_file = NULL;

	/**
	 * @var   string  destination web file
	 */
	protected $destination_web = NULL;

	/**
	 * @var  string  destination file
	 */
	protected $destination_file = NULL;

	/**
	 * @var  string condition
	 */
	protected $condition = NULL;

	/**
	 * @var  int  last modified time
	 */
	private $_last_modified = NULL;

	/**
	 * Get the source file
	 *
	 * @return string
	 */
	public function source_file()
	{
		return $this->source_file;
	}

	/**
	 * Get the web destination file
	 *
	 * @return string
	 */
	public function destination_web()
	{
		return $this->destination_web;
	}

	/**
	 * Get the destination file
	 *
	 * @return string
	 */
	public function destination_file()
	{
		return $this->destination_file;
	}
	
	/**
	 * Get the condition
	 *
	 * @return  string
	 */
	public function condition()
	{
		return $this->condition;
	}

	/**
	 * Set up the environment
	 *
	 * @param  string  $type
	 * @param  string  $file
	 * @param  array   $options
	 */
	function __construct($type, $file, array $options = array())
	{
		// Set processor to use
		$this->processor = Arr::get($options, 'processor', Kohana::$config->load('asset-merger.processor.'.$type));

		// Set condition
		$this->condition = Arr::get($options, 'condition');

		// Set type and file
		$this->type = $type;
		$this->file = $file;

		// Check if the type is a valid type
		Assets::require_valid_type($type);

		if (Valid::url($file))
		{
			// No remote files allowed
			throw new Kohana_Exception('The asset :file must be local file', array(
				':file' => $file,
			));
		}

		// Look for the specified file in each load path
		foreach ( (array) Kohana::$config->load('asset-merger.load_paths.'.$type) as $path)
		{
			if (is_file($path.$file))
			{
				// Set the destination and source file
				$this->destination_file = Assets::file_path($type, $file);
				$this->source_file      = $path.$file;

				// Don't continue
				break;
			}
		}

		if ( ! $this->source_file)
		{
			// File not found
			throw new Kohana_Exception('Asset :file of type :type not found inside :paths', array(
				':file'  => $file,
				':type'  => $type,
				':paths' => join(', ', (array) Kohana::$config->load('asset-merger.load_paths.'.$type)),
			));
		}

		if ( ! is_dir(dirname($this->destination_file)))
		{
			// Create directory for destination file
			mkdir(dirname($this->destination_file), 0777, TRUE);
		}

		// Get the file parts
		$fileparts = explode('.', basename($file));

		// Extension index
		$extension_index = array_search($this->type, $fileparts);

		// Set engines
		$this->engines = array_reverse(array_slice($fileparts, $extension_index + 1));

		// Set web destination
		$this->destination_web = Assets::web_path($type, $file);
	}

	/**
	 * Compile files
	 *
	 * @param   bool  $process
	 * @return  string
	 */
	public function compile($process = FALSE)
	{
		// Get file contents
		$content = file_get_contents($this->source_file);

		foreach ($this->engines as $engine) 
		{
			// Process content with each engine
			$content = Asset_Engine::process($engine, $content, $this);
		}

		if ($process AND $this->processor)
		{
			// Process content with processor
			$content = Asset_Processor::process($this->processor, $content);
		}

		return $content;
	}

	/**
	 * Render HTML
	 *
	 * @param   bool  $process
	 * @return  string
	 */
	public function render($process = FALSE)
	{
		if ($this->needs_recompile())
		{
			// Recompile file
			file_put_contents($this->destination_file, $this->compile($process));
		}

		return Asset::html($this->type, $this->destination_web, $this->last_modified());
	}

	/**
	 * Render inline HTML
	 *
	 * @param   bool  $process
	 * @return  string
	 */
	public function inline($process = FALSE)
	{
		return Asset::html_inline($this->type, $this->compile($process));
	}

	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Get and set the last modified time
	 *
	 * @return integer
	 */
	public function last_modified()
	{
		if ($this->_last_modified === NULL)
		{
			// Set the last modified time
			$this->_last_modified = filemtime($this->source_file);
		}

		return $this->_last_modified;
	}

	/**
	 * Determine if recompilation is needed
	 *
	 * @return bool
	 */
	public function needs_recompile()
	{
		return Assets::is_modified_later($this->destination_file, $this->last_modified());
	}

	/**
	 * Add conditions to asset
	 *
	 * @param   string  $content
	 * @param   string  $condition
	 * @return  string
	 */
	static public function conditional($content, $condition)
	{
		return "<!--[if ".$condition."]>\n". $content."\n<![endif]-->";
	}

	/**
	 * Create HTML
	 *
	 * @param   string   $type
	 * @param   string   $file
	 * @param   integer  $last_modified
	 * @return  string
	 */
	static function html($type, $file, $last_modified = NULL)
	{
		if ($last_modified)
		{
			// Add last modified time to file name
			$file = $file.'?'.$last_modified;
		}

		// Set type for the proper HTML
		switch($type)
		{
			case Assets::JAVASCRIPT:
				$type = 'script';
			break;
			case Assets::STYLESHEET:
				$type = 'style';
			break;
		}

		return HTML::$type($file);
	}

	/**
	 * Create inline HTML
	 *
	 * @param   string   $type
	 * @param   string   $content
	 * @return  string
	 */
	static function html_inline($type, $content)
	{
		// Set the proper inline HTML
		switch($type)
		{
			case Assets::JAVASCRIPT:
				$html = "<script type=\"text/javascript\">\n".$content."\n</script>";
			break;
			case Assets::STYLESHEET:
				$html = "<style>\n".$content."\n</style>";
			break;
		}

		return $html;
	}

} // End Asset