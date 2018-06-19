<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Categories;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Categories\Category;

/**
 * Category entity tests.
 *
 * @since   1.1.0
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

		static::$driver->getConnection()->exec(file_get_contents(JPATH_TESTS_PHPROBERTO . '/Unit/Categories/Stubs/Database/associations.sql'));

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
		Category::clearAll();

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
		$dataSet->addTable('jos_assets', JPATH_TESTS_PHPROBERTO . '/Unit/Categories/Stubs/Database/assets.csv');
		$dataSet->addTable('jos_categories', JPATH_TESTS_PHPROBERTO . '/Unit/Categories/Stubs/Database/categories.csv');
		$dataSet->addTable('jos_associations', JPATH_TESTS_PHPROBERTO . '/Unit/Categories/Stubs/Database/associations.csv');

		return $dataSet;
	}

	/**
	 * access retrieved.
	 *
	 * @return  void
	 */
	public function testAccessRetrieved()
	{
		$category = $this->getMockBuilder(Category::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$category->method('columnAlias')
			->willReturn('access');

		$reflection = new \ReflectionClass($category);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($category, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($category, array('id' => 999, 'access' => 0));

		$this->assertSame(0, $category->access());

		$rowProperty->setValue($category, array('id' => 999, 'access' => 1));

		$this->assertSame(1, $category->access());
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

		$asset = $category->asset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(0, $asset->id());

		$category = new Category;

		$rowProperty->setValue($category, array('id' => 999, 'asset_id' => 666));

		$asset = $category->asset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(666, $asset->id());
	}

	/**
	 * author retrieved.
	 *
	 * @return  void
	 */
	public function testAuthorRetrieved()
	{
		$category = new Category(999);

		$reflection = new \ReflectionClass($category);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($category, array('id' => 999, 'created_user_id' => 666));

		$this->assertSame(User::find(666), $category->author());
	}

	/**
	 * editor retrieved.
	 *
	 * @return  void
	 */
	public function testEditorRetrieved()
	{
		$category = new Category(999);

		$reflection = new \ReflectionClass($category);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($category, array('id' => 999, 'modified_user_id' => 666));

		$this->assertSame(User::find(666), $category->editor());
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

	/**
	 * params returns parameters.
	 *
	 * @return  void
	 */
	public function testParamsReturnsParameters()
	{
		$category = new Category(999);

		$reflection = new \ReflectionClass($category);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($category, array('id' => 999, 'params' => '{"foo":"var"}'));

		$this->assertEquals(new Registry(array('foo' => 'var')), $category->params(true));
	}

	/**
	 * table returns correct table instance.
	 *
	 * @return  void
	 */
	public function testTableReturnsCorrectTableInstance()
	{
		$category = new Category;

		$this->assertInstanceOf('CategoriesTableCategory', $category->table());
	}
}
