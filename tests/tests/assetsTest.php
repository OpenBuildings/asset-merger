<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tests for Migraiton
 * @group asset-merger
 * @group asset-merger.assets
 * @package Asset Merger
 */
class AssetMerger_assetsTest extends Testcase_Functest_Asset {

	public function data_construct_validation()
	{
		return array(
			array(array('test'), TRUE),
			array(array('test' => 'TEst'), TRUE),
			array(array(Assets::JAVASCRIPT => 'test', 'test' => 'test'), TRUE),
			array(array(Assets::JAVASCRIPT => 'test'), FALSE),
			array(array(Assets::JAVASCRIPT => 'test', Assets::STYLESHEET => 'test'), FALSE),
		);
	}

	/**
	 * @dataProvider data_construct_validation
	 */
	public function test_construct_validation($paths, $throw_exception)
	{
		Kohana::$config->load('asset-merger')->set('load_paths', $paths);
		if ($throw_exception)
		{
			$this->setExpectedException('Kohana_Exception');
		}

		new Assets();
	}

	public function test_needs_recompile()
	{
		$file = $this->data_dir().'js'.DIRECTORY_SEPARATOR.'test.js';
		$mtime = filemtime($file);

		$this->assertTrue(Assets::is_modified_later($file, $mtime + 100));
		$this->assertFalse(Assets::is_modified_later($file, $mtime - 100));
	}

	public function test_construct()
	{
		$assets = Assets::factory('test_name')->merge(FALSE)->process(TRUE);
		$this->assertEquals('test_name', $assets->name());
		$this->assertFalse($assets->merge());
		$this->assertTrue($assets->process());
	}

	public function test_require_valid_type()
	{
		$this->assertTrue(Assets::require_valid_type('js'));
		$this->assertTrue(Assets::require_valid_type('css'));

		$this->setExpectedException('Kohana_Exception');
		$this->assertTrue(Assets::require_valid_type('php'));
	}
	
	public function test_file_path_and_web_path()
	{
		$this->assertEquals($this->data_dir().'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'test.js', Assets::file_path('js', 'test.js'));

		$this->assertEquals('/assets/js/test.js', Assets::web_path('js', 'test.js'));
	}

	public function test_render()
	{
		$assets = Assets::factory('test_name');
		$this->assertEquals('', $assets->render(), 'message');

		$assets->css('test.css');
		$this->assertContains('link type="text/css" href="/assets/css/test_name.css', $assets->render());

		$assets->js('test.js');
		$this->assertContains('<script type="text/javascript" src="/assets/js/test_name.js', $assets->render());
	}

	public function test_inline()
	{
		$assets = Assets::factory('test_name')
			->process(FALSE)
			->css('test.css')
			->js('test.js');

		$inline = $assets->inline();

		$this->assertContains('.test { display: block; }', $inline);
		$this->assertContains('var test;', $inline);
	}
}