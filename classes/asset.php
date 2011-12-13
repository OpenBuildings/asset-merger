<?php 

/**
* Combines assets and merges them to single files in production
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset
{
	public $type = null;
	public $file = null;
	
	protected $engines = array();
	protected $processor = array();
	protected $source_file = null;
	protected $destination_web = null;
	protected $destination_file = null;
	protected $condition = null;

	private $_last_modified = null;

	public function source_file()
	{
		return $this->source_file;
	}

	public function destination_web()
	{
		return $this->destination_web;
	}

	public function destination_file()
	{
		return $this->destination_file;
	}
	
	public function condition()
	{
		return $this->condition;
	}

	function __construct($type, $file, $options = null)
	{
		$this->processor = Arr::get($options, 'processor', Kohana::$config->load("asset-merger.processor.$type"));
		$this->condition = Arr::get($options, 'condition');

		$this->type = $type;
		$this->file = $file;

		Assets::require_valid_type($type);

		if ( Valid::url($file) )
			throw new Kohana_Exception("The asset :file must be local file");

		foreach ((array) Kohana::$config->load("asset-merger.load_paths.$type") as $path) {
			if( is_file($path.$file))
			{
				$this->destination_file = Assets::file_path( $type, $file );
				$this->source_file = $path.$file;
				break;
			}
		}

		if( ! $this->source_file)
			throw new Kohana_Exception("Asset :file of type :type not found inside :paths", array( ":file" => $file, ":type" => $type, ":paths" => join(", ", (array) Kohana::$config->load("asset-merger.load_paths.{$type}"))));

		if( ! is_dir(dirname($this->destination_file)))
		{
			mkdir( dirname($this->destination_file) , 0777, TRUE);
		}

		$fileparts = explode('.', basename($file));
		$extension_index = array_search( $this->type, $fileparts);
		$this->engines = array_reverse(array_slice($fileparts, $extension_index + 1));

		$this->destination_web = Assets::web_path( $type, $file );
	}

	public function compile($process = null)
	{
		$content = file_get_contents($this->source_file);

		foreach ($this->engines as $engine) 
		{
			$content = Asset_Engine::process($engine, $content, $this );
		}

		if( $process AND $this->processor)
		{
			$content = Asset_Processor::process($this->processor, $content);
		}

		return $content;
	}

	public function render($process = null, $inline = TRUE)
	{
		if( $this->needs_recompile())
		{
			file_put_contents($this->destination_file, $this->compile($process));
		}
		return Asset::html( $this->type, $this->destination_web, $this->last_modified());
	}

	public function inline($process = null)
	{
		return Asset::html_inline( $this->type, $this->compile($process));
	}

	public function __toString()
	{
		return $this->render();
	}

	public function last_modified()
	{
		if( $this->_last_modified === NULL)
		{
			$this->_last_modified = filemtime( $this->source_file );
		}
		return $this->_last_modified;
	}

	public function needs_recompile()
	{
		return Assets::is_modified_later( $this->destination_file, $this->last_modified());
	}

	static public function conditional($content, $condition)
	{
		return "<!--[if ".$condition."]>\n". $content."\n<![endif]-->";
	}

	static function html($type, $file, $last_modified = null)
	{
		if( $last_modified )
		{
			$file = $file."?".$last_modified;
		}

		switch($type)
		{
			case Assets::JAVASCRIPT:
				return HTML::script($file);

			case Assets::STYLESHEET:
				return HTML::style($file);
		}
	}

	static function html_inline($type, $content)
	{
		switch($type)
		{
			case Assets::JAVASCRIPT:
				return "<script type=\"text/javascript\">\n".$content."\n</script>";

			case Assets::STYLESHEET:
				return "<style>\n".$content."\n</style>";
		}		
	}


}