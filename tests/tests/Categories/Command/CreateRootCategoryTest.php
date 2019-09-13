<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Categories\Command\CreateRootCategory;

/**
 * CreateRootCategory tests.
 *
 * @since   __DEPOY_VERSION__
 */
class CreateRootCategoryTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function categoryIsCreated()
	{
		$createdCategory = CreateRootCategory::instance()->execute();

		$this->assertInstanceOf(Category::class, $createdCategory);

		$reloaded = Category::load($createdCategory->id());

		$this->assertSame('0', $reloaded->get('level'));
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

		Factory::$config      = $this->getMockConfig();
		Factory::$session     = $this->getMockSession();
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
		$this->restoreFactoryState();

		Category::clearAll();

		parent::tearDown();
	}
}
