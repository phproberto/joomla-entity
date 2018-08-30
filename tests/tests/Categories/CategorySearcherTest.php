<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Categories\CategorySearcher;

/**
 * Category searcher tests.
 *
 * @since   1.4.0
 */
class CategorySearcherTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function accessFilterIsApplied()
	{
		$ids = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['list.limit' => 0])->search()
		);

		$this->assertFalse(in_array(46, $ids));

		$filteredIds = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['filter.access' => 2, 'list.limit' => 0])->search()
		);

		$this->assertTrue(in_array(46, $filteredIds));

		$multipleFilteredIds = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['filter.access' => [1, 2], 'list.limit' => 0])->search()
		);

		$this->assertSame($ids, array_intersect($ids, $multipleFilteredIds));
		$this->assertSame($filteredIds, array_intersect($filteredIds, $multipleFilteredIds));
		$this->assertSame(count($multipleFilteredIds), count($ids) + count($filteredIds));

		$nullAccessIds = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['filter.access' => null, 'list.limit' => 0])->search()
		);

		$this->assertSame($multipleFilteredIds, $nullAccessIds);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function descendantFilterIsApplied()
	{
		$ids = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['filter.descendant_id' => 21, 'list.limit' => 0])->search()
		);

		$this->assertSame([1,14,19,20], $ids);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ancestorFilterIsApplied()
	{
		$ids = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['filter.ancestor_id' => 20, 'list.limit' => 0])->search()
		);

		$this->assertSame([], array_diff($ids, [21,22,23,24,25,64,65,66,67,68,69,70,75]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function directionIsApplied()
	{
		$ids = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['list.limit' => 0])->search()
		);

		$this->assertNotSame(1, count($ids));
		$this->assertSame(1, $ids[0]);

		$reverseIds = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			CategorySearcher::instance(['list.direction' => 'DESC', 'list.limit' => 0])->search()
		);

		$this->assertSame(count($ids), count($reverseIds));
		$this->assertSame(1, end($reverseIds));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function extensionFilterIsApplied()
	{
		$results = CategorySearcher::instance(['filter.extension' => 'com_content', 'list.limit' => 5])->search();

		$this->assertTrue(is_array($results));
		$this->assertNotSame(0, count($results));

		foreach ($results as $categoryData)
		{
			$this->assertSame('com_content', $categoryData['extension']);
		}
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_categories', JPATH_TESTS_PHPROBERTO . '/tests/Categories/Stubs/Database/categories.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function idFilterIsApplied()
	{
		$results = CategorySearcher::instance(['filter.id' => 37])->search();

		$this->assertSame(1, count($results));

		$this->assertSame(37, (int) $results[0]['id']);

		$results = CategorySearcher::instance(['filter.id' => [37, 38]])->search();

		$this->assertSame(2, count($results));

		$this->assertSame(37, (int) $results[0]['id']);
		$this->assertSame(38, (int) $results[1]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function notIdFilterIsApplied()
	{
		$categories = CategorySearcher::instance(['list.limit' => 2])->search();

		$this->assertSame(2, count($categories));

		$categoriesNotId = CategorySearcher::instance(
			['filter.not_id' => $categories[0]['id'], 'list.limit' => 2]
		)->search();

		$this->assertSame(2, count($categories));

		$this->assertNotSame($categories[0]['id'], $categoriesNotId[0]['id']);

		$categoriesNotId = CategorySearcher::instance(
			['filter.not_id' => [$categories[0]['id'], $categories[1]['id']], 'list.limit' => 2]
		)->search();

		$this->assertSame(2, count($categories));

		$this->assertNotSame($categories[0]['id'], $categoriesNotId[0]['id']);
		$this->assertNotSame($categories[1]['id'], $categoriesNotId[1]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function orderingIsApplied()
	{
		$categories = CategorySearcher::instance()->search();

		$this->assertSame(1, (int) $categories[0]['id']);

		$categories = CategorySearcher::instance(
			[
				'filter.published' => null,
				'list.ordering'    => 'c.published'
			]
		)->search();

		$this->assertSame(42, (int) $categories[0]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentFilterIsApplied()
	{
		$categories = CategorySearcher::instance(['list.limit' => 5])->search();

		$this->assertSame(5, count($categories));

		foreach ($categories as $categoryData)
		{
			$this->assertNotSame(37, (int) $categoryData['parent_id']);
		}

		$categories = CategorySearcher::instance(['filter.parent_id' => 37, 'list.limit' => 5])->search();

		$this->assertSame(5, count($categories));

		foreach ($categories as $categoryData)
		{
			$this->assertSame(37, (int) $categoryData['parent_id']);
		}
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function publishedFilterIsApplied()
	{
		$statuses = array_unique(
			array_map(
				function ($categoryData)
				{
					return (int) $categoryData['published'];
				},
				CategorySearcher::instance(['list.limit' => 0])->search()
			)
		);

		$this->assertSame([1], $statuses);

		$unpublishedStatuses = array_unique(
			array_map(
				function ($categoryData)
				{
					return (int) $categoryData['published'];
				},
				CategorySearcher::instance(['list.limit' => 0, 'filter.published' => 0])->search()
			)
		);

		$this->assertSame([0], $unpublishedStatuses);

		$statuses = array_unique(
			array_map(
				function ($categoryData)
				{
					return (int) $categoryData['published'];
				},
				CategorySearcher::instance(['list.limit' => 0, 'filter.published' => [0, 1]])->search()
			)
		);

		$this->assertTrue(in_array(0, $statuses, true));
		$this->assertTrue(in_array(1, $statuses, true));

		$statuses = array_unique(
			array_map(
				function ($categoryData)
				{
					return (int) $categoryData['published'];
				},
				CategorySearcher::instance(['list.limit' => 0, 'filter.published' => null])->search()
			)
		);

		$this->assertTrue(in_array(0, $statuses, true));
		$this->assertTrue(in_array(1, $statuses, true));
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
		$this->restoreFactoryState();

		parent::tearDown();
	}
}
