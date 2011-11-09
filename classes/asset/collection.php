<?php 

/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan K
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Collection implements Iterator, Countable, ArrayAccess
{
	private $assets;
	
	public $name; 
	public $type;
	public $asset_file;
	public $web_file;
	private $_last_modified = null;

	public function __construct($type, $name = 'all', $processor = null)
	{
		Assets::require_valid_type($type);
		$this->type = $type;
		$this->name = $name;
		$this->asset_file = Assets::file_path($type, $name.'.'.$type);
		$this->web_file = Assets::web_path($type, $name.'.'.$type);
	}

	public function compile($process = null)
	{
		$content = '';
		foreach( $this->assets as $asset)
		{
			$content .= "/* File: ".$asset->file."\n   Compiled at: ".date("Y-m-d H:i:s")." \n================================ */\n";
			$content .= $asset->compile($process)."\n\n";
		}

		return $content;
	}

	public function render($process = null)
	{
		if ( $this->needs_recompile() )
		{
			file_put_contents($this->asset_file, $this->compile($process));
		}

		return Asset::html( $this->type, $this->web_file, $this->last_modified());
	}

	public function needs_recompile()
	{
		return Assets::is_modified_later( $this->asset_file, $this->last_modified());
	}

	public function last_modified()
	{
		if( $this->_last_modified === NULL)
		{
			$this->_last_modified = max(array_filter(self::_invoke($this->assets, 'last_modified')));
		}
		return $this->_last_modified;
	}

	static public function _invoke($arr, $method)
	{
		$new_arr = array();
		foreach($arr as $id => $item)
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
	  return isset($this->assets[$offset]) ? $this->assets[$offset] : null;
	}

	public function rewind() {
	  reset($this->assets);
	}

  public function current() {
    return current($this->assets);
  }

  public function key() {
    return key($this->assets);
  }

  public function next() {
      return next($this->assets);
  }

  public function valid() {
    return $this->current() !== false;
  }    

  public function count() {
   return count($this->assets);
  }


}