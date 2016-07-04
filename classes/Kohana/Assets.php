<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Collection of assets
*
* @package    Despark/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011-2012 Despark Ltd.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Kohana_Assets {

	public static function require_valid_type($type)
	{
		if ( ! in_array($type, array_keys(Kohana::$config->load('asset-merger.load_paths'))))
		{
			throw new Kohana_Exception('Type :type must be one of [:types]', array(
				':type'  => $type,
				':types' => join(', ', array_keys(Kohana::$config->load('asset-merger.load_paths'))))
			);
		}
		return TRUE;
	}

	/**
	 * Determine if file was modified later then source
	 *
	 * @param   string  $file
	 * @param   string  $source_modified_time
	 * @return  bool
	 */
	public static function is_modified_later($file, $source_modified_time)
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
	public static function file_path($type, $file)
	{
		// Set file
		$file = substr($file, 0, strrpos($file, $type)).$type;

		return Kohana::$config->load('asset-merger.docroot').Kohana::$config->load('asset-merger.folder').DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file;
	}

	/**
	 * Set web path
	 *
	 * @param   string  $type
	 * @param   string  $file
	 * @return  string
	 */
	public static function web_path($type, $file)
	{
		// Set file
		$file = substr($file, 0, strrpos($file, $type)).$type;

		return URL::site(Kohana::$config->load('asset-merger.folder').'/'.$type.'/'.$file);
	}

	// Default short names for types
	const JAVASCRIPT = 'js';
	const STYLESHEET = 'css';

	/**
	 * @var  bool  merge or not to merge assets
	 */
	protected $_merge = FALSE;

	/**
	 * @var  bool  process or not to process assets
	 */
	protected $_process = FALSE;

	/**
	 * @var  string  name of the merged asset file
	 */
	protected $_name;

	/**
	 * @var  array  remote assets
	 */
	protected $_remote = array();

	/**
	 * @var  array  conditional assets
	 */
	protected $_conditional = array();

	/**
	 * @var  array  regular assets
	 */
	protected $_groups = array();
	
	/**
	 * @var bool integrity check for resources
	 */
	protected $_integrity = FALSE;
	
	/**
	 * @var array hashes for integrity validation 
	 */
	protected $_hash = array();
	
	/**
	 *
	 * @var bool show paths for merged files
	 */
	protected $_showpaths = TRUE;

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
		$load_paths = Kohana::$config->load('asset-merger.load_paths');
		if ( ! $load_paths OR ! is_array($load_paths) OR count(array_diff(array_keys($load_paths), array(Assets::JAVASCRIPT, Assets::STYLESHEET))))
			throw new Kohana_Exception('You must configure load_paths for asset-merger, as array with keys Assets::JAVASCRIPT AND Assets::STYLESHEET, and values the actual load paths');
		
		$show_paths = Kohana::$config->load('asset-merger.show_paths');
		
		if (!is_bool($show_paths))
		{
			$this->_showpaths = TRUE;
		}
		else
		{
			$this->_showpaths = $show_paths;
		}
		
		$integrity = Kohana::$config->load('asset-merger.integrity_check');
		
		$hashes = array('sha256','sha384','sha512');
		
		if (!is_bool($integrity) AND is_string($integrity))
		{
			// Hash is string value
			if (in_array($integrity, $hashes))
			{
				// Set integrity to true and assing hash
				$this->_integrity = TRUE;
				$this->_hash[] = $integrity;
			}
			else
			{
				throw new Kohana_Exception('The provided hash is invalid, only one of :hashes',array(
					':hashes' => implode(', ', $hashes)
				));
			}
		}
		elseif (is_array($integrity))
		{
			foreach ($integrity as $int)
			{
				if (!in_array($int, $hashes))
				{
					throw new Kohana_Exception('Provided hash :hash is not within accepted :values',array(
						':values' => implode(', ', $hashes),
						':hash' => $int
					));
				}
			}
			
			$this->_integrity = TRUE;
			$this->_hash = $integrity;
		}
		// Else integrity is FALSE - hash must be explicit
		
		foreach (array_keys($load_paths) as $type)
		{
			// Add asset groups
			$this->_groups[$type] = new Asset_Collection($type, $name);
			// Pass displaying of merged files paths
			$this->_groups[$type]->display_paths($this->_showpaths);
		}

		// Set the merged file name
		$this->_name = $name;

		// Set process and merge
		$this->_process = $this->_merge = in_array(Kohana::$environment, (array) Kohana::$config->load('asset-merger.merge'));
	}

	public function name()
	{
		return $this->_name;
	}

	/**
	 * Get and set merge
	 *
	 * @param   NULL|bool  $merge
	 * @return  Assets|bool
	 */
	public function merge($merge = NULL)
	{
		if ($merge !== NULL)
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
		if ($process !== NULL)
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
		$html = $this->_remote;
		
		// Go through each asset group
		foreach ($this->_groups as $type => $group)
		{
			if ( ! count($group))
				continue;
			
			if ($this->merge())
			{
				// Add merged file to html
				$html[] = $group->integrity($this->_integrity, $this->_hash)->render($this->_process);
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

		foreach ($this->_conditional as $asset) 
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
		$html = $this->_remote;

		// Go through each asset group
		foreach ($this->_groups as $type => $group)
		{
			if ($this->merge())
			{
				// Add merged file to html
				$html[] = $group->inline($this->_process);
			}
			else
			{
				foreach ($group as $asset)
				{
					// Files not merged, add each of them to html
					$html[] = $asset->inline($this->_process);
				}
			}
		}

		foreach ($this->_conditional as $asset)
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
		if (Arr::get($options, 'if', TRUE))
		{

			if (Valid::url($file) OR strpos($file, '//') === 0)
			{
				if ($this->_integrity)
				{
					// Integrity for remote files
					$file_integrity_cached = Cache::instance()->get($this->integrity_key($file));
					
					if (!is_null($file_integrity_cached))
					{
						$integrity = $file_integrity_cached;
					}
					else
					{
						$integrity_string = array();
						foreach($this->_hash as $hash)
						{
							$integrity_string[] = $hash.'-'.base64_encode(hash_file($hash, $file, TRUE));
						}
						$integrity = implode(' ', $integrity_string);
						Cache::instance()->set($this->integrity_key($file), $integrity, PHP_INT_MAX);
					}
					$remote = Asset::html($type, $file, NULL, isset($options['async']), $integrity);
				}
				else
				{
					$remote = Asset::html($type, $file, NULL, isset($options['async']), $this->_integrity);
				}

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
				$this->_remote[] = $remote;
			}
			elseif (Arr::get($options, 'condition'))
			{
				// Conditional asset, add to conditionals
				$this->_conditional[] = new $class($type, $file, $options);
			}
			else
			{
				// Regular asset, add to groups
				$this->_groups[$type][] = new $class($type, $file, $options);
			}

		}

		return $this;
	}
	
	/**
	 * Generate unique integrity filename key for Caching
	 * @param string $filename
	 * @return string
	 */
	private function integrity_key($filename = NULL)
	{
		return 'Asset-Merger_' . str_replace(array('\\','/','//','\\\\'), DIRECTORY_SEPARATOR, $filename);
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

} // End Assets
