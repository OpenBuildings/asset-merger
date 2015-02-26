<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tests for Migraiton
 * @group asset-merger
 * @group asset-merger.asset
 * @package Asset Merger
 */
class AssetMerger_assetTest extends Testcase_Functest_Asset {

	public function test_construct()
	{
		$asset = new Asset(Assets::JAVASCRIPT, 'test.js');

		$this->assertEquals(self::data_dir().'js'.DIRECTORY_SEPARATOR.'test.js', $asset->source_file());
		$this->assertEquals(self::data_dir().'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'test.js', $asset->destination_file());
		$this->assertEquals('/assets/js/test.js', $asset->destination_web());
		$this->assertEquals(array(), $asset->engines());
		$this->assertEquals(Assets::JAVASCRIPT, $asset->type());

		$asset = new Asset(Assets::JAVASCRIPT, 'test.js.coffee.php');
		$this->assertEquals(array('php', 'coffee'), $asset->engines());

		$asset = new Asset(Assets::JAVASCRIPT, 'test.js', array('condition' => 'IF ie', 'processor' => 'jsmin'));
		$this->assertEquals($asset->condition(), 'IF ie');
		$this->assertEquals($asset->processor(), 'jsmin');
	}

	public function test_compile()
	{
		$asset = new Asset(Assets::JAVASCRIPT, 'test.js.test', array('processor' => 'test'));
		$compiled = $asset->compile(TRUE);

		$this->assertContains('CONVERTED', $compiled);
		$this->assertContains('PROCESSED', $compiled);
	}

	public function test_html()
	{
		$js_html = Asset::html(Assets::JAVASCRIPT, '/file.js', 'buster');
		$this->assertEquals('<script type="text/javascript" src="/file.js?buster"></script>', $js_html);

		$css_html = Asset::html(Assets::STYLESHEET, '/file.css', 'buster');
		$this->assertEquals('<link type="text/css" href="/file.css?buster" rel="stylesheet" />', $css_html);
	}

	public function test_html_inline()
	{
		$js_html = Asset::html_inline(Assets::JAVASCRIPT, 'inline-js', 'buster');
		$this->assertEquals("<script type=\"text/javascript\">\ninline-js\n</script>", $js_html);

		$css_html = Asset::html_inline(Assets::STYLESHEET, 'inline-css', 'buster');
		$this->assertEquals("<style>\ninline-css\n</style>", $css_html);
	}

	public function test_conditional()
	{
		$conditional_html = Asset::conditional('conditional-content', 'ie');
		$this->assertEquals("<!--[if ie]>\nconditional-content\n<![endif]-->", $conditional_html);
	}
}