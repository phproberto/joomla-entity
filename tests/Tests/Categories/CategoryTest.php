<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Categories\Category;

/**
 * Category entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CategoryTest extends \TestCaseDatabase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		static::$driver->getConnection()->exec(file_get_contents(JPATH_TESTS_PHPROBERTO . '/Tests/Categories/Stubs/Database/associations.sql'));

		\JFactory::$database = static::$driver;
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

		\JFactory::$session     = $this->getMockSession();
		\JFactory::$config      = $this->getMockConfig();
		\JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Category::clearAllInstances();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_assets', JPATH_TESTS_PHPROBERTO . '/Tests/Categories/Stubs/Database/assets.csv');
		$dataSet->addTable('jos_categories', JPATH_TESTS_PHPROBERTO . '/Tests/Categories/Stubs/Database/categories.csv');
		$dataSet->addTable('jos_associations', JPATH_TESTS_PHPROBERTO . '/Tests/Categories/Stubs/Database/associations.csv');

		return $dataSet;
	}

	/**
	 * Asset can be retrieved.
	 *
	 * @return  void
	 */
	public function testAssetCanBeRetrieved()
	{
		$category = new Category;
		$reflection = new \ReflectionClass($category);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($category, array('id' => 999));

		$asset = $category->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(0, $asset->id());

		$category = new Category;

		$rowProperty->setValue($category, array('id' => 999, 'asset_id' => 666));

		$asset = $category->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(666, $asset->id());
	}

	/**
	 * loadAssociations returns empty array.
	 *
	 * @return  void
	 */
	public function testLoadAssociationsReturnsEmptyArrayForNonLoadedCategory()
	{
		$category = new Category;

		$reflection = new \ReflectionClass($category);
		$method = $reflection->getMethod('loadAssociations');
		$method->setAccessible(true);

		$this->assertEquals(array(), $method->invoke($category));
	}

	/**
	 * loadAssociations returns correct data.
	 *
	 * @return  void
	 */
	public function testLoadAssociationsReturnsCorrectData()
	{
		$category = new Category(35);

		$reflection = new \ReflectionClass($category);
		$method = $reflection->getMethod('loadAssociations');
		$method->setAccessible(true);

		$associations = $method->invoke($category);

		$this->assertEquals(34, $associations['en-GB']->id());
	}

	/**
	 * loadTranslations returns empty collection for missing associations.
	 *
	 * @return  void
	 */
	public function testLoadTranslationsReturnsEmptyCollectionForMissingAssociations()
	{
		$category = new Category;

		$reflection = new \ReflectionClass($category);
		$method = $reflection->getMethod('loadTranslations');
		$method->setAccessible(true);

		$this->assertEquals(new Collection, $method->invoke($category));
	}

	/**
	 * loadTranslations returns correct data for existing associations.
	 *
	 * @return  void
	 */
	public function testLoadTranslationsReturnsCorrectDataForExistingTranslations()
	{
		$category = $this->getMockBuilder(Category::class)
			->setMethods(array('associationsIds'))
			->getMock();

		$category
			->expects($this->once())
			->method('associationsIds')
			->willReturn(array(34, 35));

		$reflection = new \ReflectionClass($category);

		$method = $reflection->getMethod('loadTranslations');
		$method->setAccessible(true);

		$this->assertEquals(array(34, 35), $method->invoke($category)->ids());
	}
}
