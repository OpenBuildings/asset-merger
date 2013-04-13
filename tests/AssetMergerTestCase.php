<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @group asset-merger
 * @package Asset Merger
 */
Class AssetMergerTestCase extends Unittest_TestCase
{
	static protected $_data_dir;

	static protected function data_dir()
	{
		if ( ! self::$_data_dir)
		{
			self::$_data_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'test_data'.DIRECTORY_SEPARATOR;
		}
		return self::$_data_dir;
	}

	public function setUp()
	{
		$this->environmentDefault = array(
			'asset-merger.merge' => TRUE,
			'asset-merger.docroot' => $this->data_dir(),
			'asset-merger.folder' => 'assets',
			'asset-merger.load_paths.js' => $this->data_dir().'js'.DIRECTORY_SEPARATOR,
			'asset-merger.load_paths.css' => $this->data_dir().'css'.DIRECTORY_SEPARATOR,
		);
		parent::setUp();
	}
}