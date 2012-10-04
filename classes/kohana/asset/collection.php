<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Collection of assets
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Collection implements Iterator, Countable, ArrayAccess {

	/**
	 * @var  array  assets
	 */
	private $assets = array();

	/**
	 * @var  string  name
	 */
	public $name;

	/**
	 * @var  string  type
	 */
	public $type;

	/**
	 * @var  string  asset file
	 */
	public $asset_file;

	/**
	 * @var   string  web file
	 */
	public $web_file;

	/**
	 * @var  int  last modified time
	 */
	private $_last_modified = NULL;

	/**
	 * Set up environment
	 *
	 * @param  string  $type
	 * @param  string  $name
	 */
	public function __construct($type, $name = 'all')
	{
		// Check type
		Assets::require_valid_type($type);

		// Set type and name
		$this->type = $type;
		$this->name = $name;

		// Set asset file and web file
		$this->asset_file = Assets::file_path($type, $name.'.'.$type);
		$this->web_file   = Assets::web_path($type, $name.'.'.$type);
	}

	/**
	 * Compile asset content
	 *
	 * @param   bool  $process
	 * @return  string
	 */
	public function compile($process = FALSE)
	{
		// Set content
		$content = '';

		foreach ($this->assets as $asset)
		{
			// Add comment to content
			$content .= "/* File: ".$asset->file."\n   Compiled at: ".date("Y-m-d H:i:s")." \n================================ */\n";

			// Compile content
			$content .= $asset->compile($process)."\n\n";
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
			file_put_contents($this->asset_file, $this->compile($process));
		}

		return Asset::html($this->type, $this->web_file, $this->last_modified());
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

	/**
	 * Determine if recompilation is needed
	 *
	 * @return bool
	 */
	public function needs_recompile()
	{
		return Assets::is_modified_later($this->asset_file, $this->last_modified());
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
			// Get last modified times
			$last_modified_times = array_filter(self::_invoke($this->assets, 'last_modified'));

			if ( ! empty($last_modified_times))
			{
				// Set the last modified time
				$this->_last_modified = max($last_modified_times);
			}
		}

		return $this->_last_modified;
	}

	static public function _invoke($arr, $method)
	{
		$new_arr = array();

		foreach ($arr as $id => $item)
		{
			$new_arr[$id] = $item->$method();
		}

		return $new_arr;
	}	

	public function offsetSet($offset, $value) 
	{
		if (is_null($offset))
		{
			$this->assets[] = $value;
		}
		else
		{
			$this->assets[$offset] = $value;
		}
	}

	public function offsetExists($offset) 
	{
		return isset($this->assets[$offset]);
	}

	public function offsetUnset($offset) 
	{
		unset($this->assets[$offset]);
	}

	public function offsetGet($offset) 
	{
		return isset($this->assets[$offset]) ? $this->assets[$offset] : NULL;
	}

	public function rewind()
	{
		reset($this->assets);
	}

	public function current()
	{
		return current($this->assets);
	}

	public function key()
	{
		return key($this->assets);
	}

	public function next()
	{
		return next($this->assets);
	}

	public function valid()
	{
		return $this->current() !== FALSE;
	}

	public function count()
	{
		return count($this->assets);
	}

} // End Asset_Collection