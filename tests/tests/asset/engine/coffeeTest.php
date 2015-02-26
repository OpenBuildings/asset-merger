<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tests for Migraiton
 * @group asset-merger
 * @group asset-merger.asset.engine
 * @group asset-merger.asset.engine.coffee
 * @package Asset Merger
 */
class AssetMerger_Asset_Engine_CoffeeTest extends Testcase_Functest_Asset {

	public function test_process()
	{
		$asset = new Asset(Assets::JAVASCRIPT, 'test-coffee.js.coffee');

		$coffee = file_get_contents($asset->source_file());
		$js = file_get_contents($asset->destination_file());
		
		$converted = Asset_Engine_Coffee::process($coffee, $asset);
		$this->assertEquals($js, $converted);
	}

}