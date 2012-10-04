<?php defined('SYSPATH') OR die('No direct script access.');

class Unittest_Asset_TestCase extends Unittest_TestCase {

	static protected $_test_data_dir;

	static protected function test_data_dir()
	{
		if ( ! self::$_test_data_dir)
		{
			self::$_test_data_dir = realpath(join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', '..', 'test_data'))).DIRECTORY_SEPARATOR;
		}
		return self::$_test_data_dir;
	}

	public function setUp()
	{
		$this->environmentDefault = array(
			'asset-merger.merge' => TRUE,
			'asset-merger.docroot' => $this->test_data_dir(),
			'asset-merger.folder' => 'assets',
			'asset-merger.load_paths.js' => $this->test_data_dir().'js'.DIRECTORY_SEPARATOR,
			'asset-merger.load_paths.css' => $this->test_data_dir().'css'.DIRECTORY_SEPARATOR,
		);
		parent::setUp();
	}


} // End Unittest_Asset_TestCase