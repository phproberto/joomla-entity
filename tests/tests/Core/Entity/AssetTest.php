<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Entity;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Core\Entity\Asset;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;

/**
 * Asset entity tests.
 *
 * @since   1.1.0
 */
class AssetTest extends \TestCaseDatabase
{
	/**
	 * instance loads an asset.
	 *
	 * @return  void
	 */
	public function testInstanceLoadsAnAsset()
	{
		$asset = Asset::find(1);

		$this->assertEquals(1, $asset->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function crudWorks()
	{
		$asset = Asset::create(['name' => 'joomla.entity', 'title' => 'createWorks']);

		$this->assertTrue($asset->hasId());

		$asset->assign('name', 'joomla.entity.edited');
		$asset->save();

		$id = $asset->id();
		Asset::clear($id);
		$reloadedAsset = Asset::load($id);

		$this->assertSame('joomla.entity.edited', $reloadedAsset->get('name'));

		Asset::clear($id);
		Asset::delete($id);

		$error = '';

		try
		{
			$reloadedAsset = Asset::load($id);
		}
		catch (LoadEntityDataError $e)
		{
			$error = $e->getMessage();
		}

		$this->assertNotEmpty($error);
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_assets', JPATH_TEST_DATABASE . '/jos_assets.csv');

		return $dataSet;
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		Factory::$session     = $this->getMockSession();
		Factory::$config      = $this->getMockConfig();
		Factory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Asset::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
