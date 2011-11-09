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

	private $assets;
	private $_merge = false;
	private $_process = false;
	private $_name;

	private $remote = array();
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
		return join("\n", $html);
		
	}

	public function add($type, $file, $processor = null)
	{
		if( Valid::url($file) )
		{
			$this->remote[] = Asset::html($type, $file);
		}
		else
		{
			$this->groups[$type][] = new Asset($type, $file, $processor);
		}
		return $this;
	}

	public function css($file, $processor = null)
	{
		return $this->add(Assets::STYLESHEET, $file, $processor);
	}

	public function js($file, $processor = null)
	{
		return $this->add(Assets::JAVASCRIPT, $file, $processor);
	}

	public function js_block($script, $processor = null)
	{
		$this->groups[Assets::JAVASCRIPT][] = new Asset_Block(Assets::JAVASCRIPT, $script, $processor);
		return $this;
	}

	public function css_block($css, $processor = null)
	{
		$this->groups[Assets::STYLESHEET][] = new Asset_Block(Assets::STYLESHEET, $css, $processor);
		return $this;
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
		return DOCROOT.Kohana::$config->load("asset-merger.folder").DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file;
	}

	static public function web_path( $type, $file)
	{
		return Kohana::$config->load("asset-merger.folder").'/'.$type.'/'.$file;
	}



}