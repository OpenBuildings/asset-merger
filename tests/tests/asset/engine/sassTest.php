<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tests for Migraiton
 * @group asset-merger
 * @group asset-merger.asset.engine
 * @group asset-merger.asset.engine.sass
 * @package Asset Merger
 */
class AssetMerger_Asset_Engine_sassTest extends Testcase_Functest_Asset {

	public function test_process()
	{
		$asset = new Asset(Assets::STYLESHEET, 'test-sass.css.sass');

		$sass = file_get_contents($asset->source_file());
		$css = file_get_contents($asset->destination_file());
		
		$converted = Asset_Engine_Sass::process($sass, $asset);
		$this->assertEquals($css, $converted);
	}

}