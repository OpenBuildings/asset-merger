<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tests for Migraiton
 * @group asset-merger
 * @group asset-merger.asset
 * @group asset-merger.asset.collection
 * @package Asset Merger
 */
class AssetMerger_Asset_CollectionTest extends Testcase_Functest_Asset {

	public function test_construct()
	{
		$collection = new Asset_Collection(Assets::JAVASCRIPT, 'test_name');

		$this->assertEquals(Assets::JAVASCRIPT, $collection->type());
		$this->assertEquals('test_name', $collection->name());
		$this->assertEquals($this->data_dir().'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'test_name.js', $collection->destination_file());
		$this->assertEquals('/assets/js/test_name.js', $collection->destination_web());
	}

}