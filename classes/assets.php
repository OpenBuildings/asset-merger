<?php defined('SYSPATH') or die('No direct script access.');
/**
* Collection of assets
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Assets {

	// Default short names for types
	const JAVASCRIPT = 'js';
	const STYLESHEET = 'css';

	/**
	 * @var  array  collections of assets
	 */
	private $assets = array();

	/**
	 * @var  bool  merge or not to merge assets
	 */
	private $_merge = FALSE;

	/**
	 * @var  bool  process or not to process assets
	 */
	private $_process = FALSE;

	/**
	 * @var  string  name of the merged asset file
	 */
	private $_name;

	/**
	 * @var  array  remote assets
	 */
	private $remote = array();

	/**
	 * @var  array  conditional assets
	 */
	private $conditional = array();

	/**
	 * @var  array  regular assets
	 */
	private $groups = array();

	/**
	 * Return a new Assets object
	 *
	 * @param   $name   string
	 * @return  Assets
	 */
	static public function factory($name)
	{
		return new Assets($name);
	}

	/**
	 * Create the asset groups, set the file name and enable / disable process
	 * and merge
	 *
	 * @param string $name
	 */
	public function __construct($name = 'all')
	{
		foreach (array_keys(Kohana::$config->load('asset-merger.load_paths')) as $type)
		{
			// Add asset groups
			$this->groups[$type] = new Asset_Collection($type, $name);
		}

		// Set the merged file name
		$this->_name = $name;

		// Set process and merge
		$this->_process = $this->_merge = in_array(Kohana::$environment, (array) Kohana::$config->load('asset-merger.merge'));
	}

	/**
	 * Get and set merge
	 *
	 * @param   NULL|bool  $merge
	 * @return  Assets|bool
	 */
	public function merge($merge = NULL)
	{
		if ($merge)
		{
			// Set merge
			$this->_merge = (bool) $merge;

			return $this;
		}

		// Return merge
		return $this->_merge;
	}

	/**
	 * Get and set process
	 *
	 * @param   NULL|bool $process
	 * @return  Assets|bool
	 */
	public function process($process = NULL)
	{
		if ($process)
		{
			// Set process
			$this->_process = (bool) $process;

			return $this;
		}

		// Return process
		return $this->_process;
	}	

	function __toString()
	{
		return $this->render();
	}

	/**
	 * Renders the HTML code
	 *
	 * @return string
	 */
	public function render()
	{
		// Set html
		$html = $this->remote;

		// Go through each asset group
		foreach ($this->groups as $type => $group)
		{
			if ($this->merge())
			{
				// Add merged file to html
				$html[] = $group->render($this->_process);
			}
			else
			{
				foreach($group as $asset)
				{
					// Files not merged, add each of them to html
					$html[] = $asset->render($this->_process);		
				}
			}
		}

		foreach ($this->conditional as $asset) 
		{
			// Add conditional assets
			$html[] .= Asset::conditional($asset->render($this->_process), $asset->condition());
		}

		// Return html
		return join("\n", $html);
	}

	/**
	 * Renders inline HTML code
	 *
	 * @return string
	 */
	public function inline()
	{
		// Set html
		$html = $this->remote;

		// Go through each asset group
		foreach ($this->groups as $type => $group)
		{
			if ($this->merge())
			{
				// Add merged file to html
				$html[] = $group->inline($this->_process);
			}
			else
			{
				foreach($group as $asset)
				{
					// Files not merged, add each of them to html
					$html[] = $asset->inline($this->_process);
				}
			}
		}

		foreach ($this->conditional as $asset)
		{
			// Add conditional assets
			$html[] .= Asset::conditional($asset->inline($this->_process), $asset->condition());
		}

		// Return html
		return join("\n", $html);
	}	

	/**
	 * Adds assets to the appropriate type
	 *
	 * @param   string  $class
	 * @param   string  $type
	 * @param   string  $file
	 * @param   array   $options
	 * @return  Assets
	 */
	protected function add($class, $type, $file, array $options = array())
	{
		if (Valid::url($file))
		{
			// Remote asset
			$remote = Asset::html($type, $file);

			if ($condition = Arr::get($options, 'condition'))
			{
				// Remote asset with conditions
				$remote = Asset::conditional($remote, $condition);
			}

			if ($type === Assets::JAVASCRIPT AND $fallback = Arr::get($options, 'fallback'))
			{
				if ( ! is_array($fallback))
					throw new Kohana_Exception("Fallback must be an array of 'check' and 'local path'. Check is evaluated to see if we need to include the local path");

				// Remote asset with conditions
				$remote = Asset::fallback($remote, $fallback[0], $fallback[1]);
			}

			// Add to remote
			$this->remote[] = $remote;
		}
		elseif (Arr::get($options, 'condition'))
		{
			// Conditional asset, add to conditionals
			$this->conditional[] = new $class($type, $file, $options);
		}
		else
		{
			// Regular asset, add to groups
			$this->groups[$type][] = new $class($type, $file, $options);
		}

		return $this;
	}

	/**
	 * Add stylesheet
	 *
	 * @param   string  $file
	 * @param   array   $options
	 * @return  Assets
	 */
	public function css($file, array $options = array())
	{
		return $this->add('Asset', Assets::STYLESHEET, $file, $options);
	}

	/**
	 * Add javascript
	 *
	 * @param   string  $file
	 * @param   array   $options
	 * @return  Assets
	 */
	public function js($file, array $options = array())
	{
		return $this->add('Asset', Assets::JAVASCRIPT, $file, $options);
	}

	/**
	 * Add javascript block
	 *
	 * @param   string  $script
	 * @param   array   $options
	 * @return  Assets
	 */
	public function js_block($script, array $options = array())
	{
		return $this->add('Asset_Block', Assets::JAVASCRIPT, $script, $options);
	}

	/**
	 * Add stylesheet block
	 *
	 * @param   string  $css
	 * @param   array   $options
	 * @return  Assets
	 */
	public function css_block($css, array $options = array())
	{
		return $this->add('Asset_Block', Assets::STYLESHEET, $css, $options);
	}

	static public function require_valid_type($type)
	{
		if ( ! in_array($type, array_keys(Kohana::$config->load('asset-merger.load_paths'))))
		{
			throw new Kohana_Exception('Type :type must be one of [:types]', array(
				':type'  => $type,
				':types' => join(', ', array_keys(Kohana::$config->load('asset-merger.load_paths'))))
			);
		}
	}

	/**
	 * Determine if file was modified later then source
	 *
	 * @param   string  $file
	 * @param   string  $source_modified_time
	 * @return  bool
	 */
	static public function is_modified_later($file, $source_modified_time)
	{
		return ( ! is_file($file) OR filemtime($file) < $source_modified_time);
	}

	/**
	 * Set file path
	 *
	 * @param   string  $type
	 * @param   string  $file
	 * @return  string
	 */
	static public function file_path($type, $file)
	{
		// Set file
		$file = substr($file, 0, strrpos($file, $type)).$type;

		return DOCROOT.Kohana::$config->load('asset-merger.folder').DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file;
	}

	/**
	 * Set web path
	 *
	 * @param   string  $type
	 * @param   string  $file
	 * @return  string
	 */
	static public function web_path($type, $file)
	{
		// Set file
		$file = substr($file, 0, strrpos($file, $type)).$type;

		return Kohana::$config->load('asset-merger.folder').'/'.$type.'/'.$file;
	}

} // End Assets