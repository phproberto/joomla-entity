<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Translation\Contracts\Translatable;

/**
 * Category entity tests.
 *
 * @since   1.1.0
 */
class CategoryTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function ancestorsReturnsExpectedValue()
	{
		$category = new Category;

		$this->assertTrue($category->ancestors()->isEmpty());

		$category = Category::find(21);

		$this->assertSame([1,14,19,20], $category->ancestors()->ids());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function descendantsReturnsExpectedValue()
	{
		$category = new Category;

		$this->assertTrue($category->descendants()->isEmpty());

		$category = Category::find(20);

		$this->assertSame([], array_diff($category->descendants()->ids(), [21,22,23,24,25,64,65,66,67,68,69,70,75]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function childrenReturnsChildrenCategories()
	{
		$children = Category::find(37)->children();

		$this->assertInstanceOf(Collection::class, $children);
		$this->assertFalse($children->isEmpty());

		$this->assertFalse(in_array(22, $children->ids()));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function implementsTranslatable()
	{
		$category = new Category;

		$this->assertTrue($category instanceof Translatable);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function levelReturnsCategoryLevel()
	{
		$this->assertSame(2, Category::find(19)->level());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentReturnsCorrectParent()
	{
		$parent = Category::find(19)->parent();

		$this->assertInstanceOf(Category::class, $parent);
		$this->assertSame(14, $parent->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchChildrenReturnsEmptyCollectionForEntityWithoutId()
	{
		$category = new Category;
		$children = $category->children();

		$this->assertInstanceOf(Collection::class, $children);
		$this->assertSame(0, $children->count());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchChildrenFilters()
	{
		$category = Category::find(37);
		$children = $category->searchChildren(['filter.published' => 1]);

		$this->assertInstanceOf(Collection::class, $children);
		$this->assertFalse(in_array(42, $children->ids()));

		$unpublishedChildren = $category->searchChildren(
			[
				'filter.published' => 0
			]
		);

		$this->assertTrue(in_array(42, $unpublishedChildren->ids()));
		$this->assertSame([], array_intersect($children->ids(), $unpublishedChildren->ids()));

		$allChildren = $category->searchChildren(['filter.published' => [0, 1]]);

		$this->assertSame($children->ids(), array_intersect($children->ids(), $allChildren->ids()));
		$this->assertSame($unpublishedChildren->ids(), array_intersect($unpublishedChildren->ids(), $allChildren->ids()));

		$allWithNullChildren = $category->searchChildren(['filter.published' => null]);

		$this->assertSame($allChildren->ids(), $allWithNullChildren->ids());
	}

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		static::$driver->getConnection()->exec(file_get_contents(JPATH_TESTS_PHPROBERTO . '/tests/Categories/Stubs/Database/associations.sql'));

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
		$dataSet->addTable('jos_assets', JPATH_TESTS_PHPROBERTO . '/tests/Categories/Stubs/Database/assets.csv');
		$dataSet->addTable('jos_categories', JPATH_TESTS_PHPROBERTO . '/tests/Categories/Stubs/Database/categories.csv');
		$dataSet->addTable('jos_associations', JPATH_TESTS_PHPROBERTO . '/tests/Categories/Stubs/Database/associations.csv');

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
