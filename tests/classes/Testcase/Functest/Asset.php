<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Testcase_Functest_Asset extends Testcase_Functest {

	static protected $_data_dir;

	static protected function data_dir()
	{
		if ( ! self::$_data_dir)
		{
			self::$_data_dir = realpath(join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', '..', 'test_data'))).DIRECTORY_SEPARATOR;
		}
		return self::$_data_dir;
	}

	public function setUp()
	{
		parent::setUp();

		$this->environment()->backup_and_set(array(
			'asset-merger.merge' => TRUE,
			'asset-merger.docroot' => $this->data_dir(),
			'asset-merger.folder' => 'assets',
			'asset-merger.load_paths.js' => $this->data_dir().'js'.DIRECTORY_SEPARATOR,
			'asset-merger.load_paths.css' => $this->data_dir().'css'.DIRECTORY_SEPARATOR,
		));
	}
}