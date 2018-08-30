<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Core\Asset;
use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Core\Module;
use Phproberto\Joomla\Entity\Core\Client\Site;
use Phproberto\Joomla\Entity\Core\Client\Administrator;

/**
 * Module tests.
 *
 * @since   1.4.0
 */
class ModuleTest extends \TestCaseDatabase
{
	private $module;

	/**
	 * @test
	 *
	 * @return void
	 */
	public function accessReturnsExpectedValue()
	{
		$this->assertSame(1, $this->module->access());
		$this->assertSame(3, Module::find(3)->access());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function assetReturnsCorrectAsset()
	{
		$this->module->assign($this->module->columnAlias(Column::ASSET), 2);

		$asset = $this->module->asset();

		$this->assertInstanceOf(Asset::class, $asset);
		$this->assertSame('com_admin', $asset->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function clientReturnsExpectedClient()
	{
		$client = $this->module->client();

		$this->assertInstanceOf(Site::class, $client);

		$this->assertInstanceOf(Administrator::class, Module::find(2)->client());
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
		$dataSet->addTable('jos_modules', JPATH_TEST_DATABASE . '/jos_modules.csv');
		$dataSet->addTable('jos_modules_menu', JPATH_TEST_DATABASE . '/jos_modules_menu.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPublishedInMenuReturnsTrueForNoMenuIdIfModuleIsShownOnAllPages()
	{
		$module = new Module;
		$reflection = new \ReflectionClass($module);
		$menusIdsProperty = $reflection->getProperty('menusIds');
		$menusIdsProperty->setAccessible(true);

		$menusIdsProperty->setValue($module, [0]);

		$this->assertTrue($module->isPublishedInMenu(0));
		$this->assertTrue($module->isPublishedInMenu(null));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPublishedInMenuReturnsFalseForNoMenuIdIfModuleIsNotShownOnAllPages()
	{
		$module = new Module;
		$reflection = new \ReflectionClass($module);
		$menusIdsProperty = $reflection->getProperty('menusIds');
		$menusIdsProperty->setAccessible(true);

		$menusIdsProperty->setValue($module, [222]);

		$this->assertFalse($module->isPublishedInMenu(0));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPublishedInMenuReturnsFalseForNoMenuIdInMenusIds()
	{
		$module = new Module;
		$reflection = new \ReflectionClass($module);
		$menusIdsProperty = $reflection->getProperty('menusIds');
		$menusIdsProperty->setAccessible(true);

		$menusIdsProperty->setValue($module, [222, 444]);

		$this->assertTrue($module->isPublishedInMenu(222));
		$this->assertFalse($module->isPublishedInMenu(333));
		$this->assertTrue($module->isPublishedInMenu(444));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPublishedInMenuReturnsFalseForNoMenusIds()
	{
		$module = new Module;
		$reflection = new \ReflectionClass($module);
		$menusIdsProperty = $reflection->getProperty('menusIds');
		$menusIdsProperty->setAccessible(true);

		$menusIdsProperty->setValue($module, []);

		$this->assertFalse($module->isPublishedInMenu(222));
		$this->assertFalse($module->isPublishedInMenu(333));
		$this->assertFalse($module->isPublishedInMenu(444));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPublishedInMenuReturnsTrueForExcludedMenuId()
	{
		$module = new Module;
		$reflection = new \ReflectionClass($module);
		$menusIdsProperty = $reflection->getProperty('menusIds');
		$menusIdsProperty->setAccessible(true);

		$menusIdsProperty->setValue($module, [-222, -555]);

		$this->assertFalse($module->isPublishedInMenu(222));
		$this->assertTrue($module->isPublishedInMenu(333));
		$this->assertFalse($module->isPublishedInMenu(555));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPublishedReturnsExpectedValue()
	{
		$this->assertTrue($this->module->isPublished());
		$this->assertFalse($this->module->isUnpublished());

		$this->assertFalse(Module::find(79)->isPublished());
		$this->assertTrue(Module::find(79)->isUnpublished());

		// Not published up
		$date = new \DateTime;
		$date->modify('+1 hour');

		// Future publish_up date
		$this->module->assign($this->module->columnAlias(Column::PUBLISH_UP), $date->format('Y-m-d H:i:s'));

		$this->assertFalse($this->module->isPublished());
		$this->assertTrue($this->module->isUnpublished());

		$this->module->assign($this->module->columnAlias(Column::PUBLISH_UP), null);

		$this->assertTrue($this->module->isPublished());
		$this->assertFalse($this->module->isUnpublished());

		// Past publish_down date
		$date = new \DateTime;
		$date->modify('-1 hour');

		$this->module->assign($this->module->columnAlias(Column::PUBLISH_DOWN), $date->format('Y-m-d H:i:s'));

		$this->assertFalse($this->module->isPublished());
		$this->assertTrue($this->module->isUnpublished());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function menusIdsReturnsExpectedVale()
	{
		$module = new Module;
		$this->assertEquals([], $module->menusIds());
		$this->assertEquals([101], $this->module->menusIds());
		$this->assertEquals([0], Module::find(2)->menusIds());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function menusIdsReturnsCachedInstanceAndReloads()
	{
		$reflection = new \ReflectionClass($this->module);
		$menusIdsProperty = $reflection->getProperty('menusIds');
		$menusIdsProperty->setAccessible(true);

		$menusIdsProperty->setValue($this->module, [999, 666]);

		$this->assertEquals([999, 666], $this->module->menusIds());
		$this->assertEquals([101], $this->module->menusIds(true));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadWorks()
	{
		$this->assertSame('Main Menu', $this->module->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function paramsReturnsModuleParameters()
	{
		$params = $this->module->params();

		$this->assertInstanceOf(Registry::class, $params);
		$this->assertSame('mainmenu', $params->get('menutype'));
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

		$this->module = Module::find(1);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Module::clearAll();

		parent::tearDown();
	}
}
