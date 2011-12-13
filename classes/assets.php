<?php 

/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Assets
{
	const JAVASCRIPT = 'js';
	const STYLESHEET = 'css';

	private $assets = array();
	private $_merge = false;
	private $_process = false;
	private $_name;

	private $remote = array();
	private $conditional = array();
	private $groups = array();

	static public function factory( $name )
	{
		return new Assets($name);
	}

	public function __construct($name = 'all')
	{
		foreach (array_keys(Kohana::$config->load("asset-merger.load_paths")) as $type) {
			$this->groups[$type] = new Asset_Collection($type, $name);
		}
		$this->_name = $name;
		$this->_process = $this->_merge = in_array(Kohana::$environment, (array) Kohana::$config->load('asset-merger.merge'));
	}

	public function merge($merge = NULL)
	{
		if( $merge !== NULL)
		{
			$this->_merge = (bool) $merge;
			return $this;
		}
		else
		{
			return $this->_merge;
		}
	}

	public function process($process = NULL)
	{
		if( $process !== NULL)
		{
			$this->_process = (bool) $process;
			return $this;
		}
		else
		{
			return $this->_process;
		}
	}	

	function __toString()
	{
		return $this->render();
	}

	public function render()
	{
		$html = $this->remote;
		foreach ($this->groups as $type => $group) 
		{
			if($this->merge())
			{
				$html[] = $group->render($this->_process);
			}
			else
			{
				foreach($group as $asset)
				{
					$html[] = $asset->render($this->_process);		
				}
			}
		}

		foreach ($this->conditional as $asset) 
		{
			$html[] .= "<!--[ if ".$asset->condition().']>'. $asset->render($this->_process).'<![endif]-->';
		}

		return join("\n", $html);
	}

	public function inline()
	{
		$html = $this->remote;
		foreach ($this->groups as $type => $group) 
		{
			if($this->merge())
			{
				$html[] = $group->inline($this->_process);
			}
			else
			{
				foreach($group as $asset)
				{
					$html[] = $asset->inline($this->_process);		
				}
			}
		}

		foreach ($this->conditional as $asset) 
		{
			$html[] .= Asset::conditional($asset->inline($this->_process), $asset->condition());
		}

		return join("\n", $html);
	}	

	protected function add($class, $type, $file, $options = null)
	{
		if( Valid::url($file) )
		{
			$remote = Asset::html($type, $file);

			if($condition = Arr::get($options, 'condition'))
			{
				$remote = Asset::conditional($remote, $condition);
			}

			$this->remote[] = $remote;
		}
		elseif(Arr::get($options, 'condition'))
		{
			$this->conditional[] = new $class($type, $file, $options);
		}
		else
		{
			$this->groups[$type][] = new $class($type, $file, $options);
		}
		return $this;
	}

	public function css($file, $options = null)
	{
		return $this->add('Asset', Assets::STYLESHEET, $file, $options);
	}

	public function js($file, $options = null)
	{
		return $this->add('Asset', Assets::JAVASCRIPT, $file, $options);
	}

	public function js_block($script, $options = null)
	{
		return $this->add('Asset_Block', Assets::JAVASCRIPT, $script, $options);
	}

	public function css_block($css, $processor = null)
	{
		return $this->add('Asset_Block', Assets::STYLESHEET, $css, $options);
	}


	static public function require_valid_type($type)
	{
		if( ! in_array($type, array_keys(Kohana::$config->load("asset-merger.load_paths"))))
			throw new Kohana_Exception("Type :type must be one of [:types]", array(":type" => $type, ":types" => join(', ', array_keys(Kohana::$config->load("asset-merger.load_paths")))));

	}

	static public function is_modified_later( $file, $source_modified_time)
	{
		return 
			! is_file( $file )
			||
			filemtime( $file ) < $source_modified_time;
	}

	static public function file_path( $type, $file )
	{
		$file = substr($file, 0, strrpos($file, $type)) . $type;

		return DOCROOT.Kohana::$config->load("asset-merger.folder").DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file;
	}

	static public function web_path( $type, $file)
	{
		$file = substr($file, 0, strrpos($file, $type)) . $type;

		return Kohana::$config->load("asset-merger.folder").'/'.$type.'/'.$file;
	}



}