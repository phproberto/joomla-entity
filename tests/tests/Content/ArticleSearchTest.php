<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Content\ArticleSearch;

/**
 * ArticleSearch tests.
 *
 * @since   __DEPLOY_VERSION_
 */
class ArticleSearchTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function accessFilterIsApplied()
	{
		$ids = array_map(
			function ($itemData)
			{
				return (int) $itemData['id'];
			},
			ArticleSearch::instance(
				[
					'filter.access' => 1,
					'list.limit' => 0
				]
			)->search()
		);

		$this->assertFalse(in_array(2, $ids));

		$filteredIds = array_map(
			function ($itemData)
			{
				return (int) $itemData['id'];
			},
			ArticleSearch::instance(
				[
					'filter.access' => 2,
					'list.limit' => 0
				]
			)->search()
		);

		$this->assertTrue(in_array(2, $filteredIds));

		$multipleFilteredIds = array_map(
			function ($itemData)
			{
				return (int) $itemData['id'];
			},
			ArticleSearch::instance(['filter.access' => [1, 2], 'list.limit' => 0])->search()
		);

		$this->assertSame($ids, array_intersect($ids, $multipleFilteredIds));
		$this->assertSame($filteredIds, array_intersect($filteredIds, $multipleFilteredIds));
		$this->assertSame(count($multipleFilteredIds), count($ids) + count($filteredIds));

		$nullAccessIds = array_map(
			function ($itemData)
			{
				return (int) $itemData['id'];
			},
			ArticleSearch::instance(['filter.access' => null, 'list.limit' => 0])->search()
		);

		$this->assertSame([], array_diff($multipleFilteredIds, $nullAccessIds));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function activeUserAccessFilterIsApplied()
	{
		$user = $this->getMockBuilder(User::class)
			->setMethods(array('getAuthorisedViewLevels'))
			->getMock();

		$user->method('getAuthorisedViewLevels')
			->will($this->onConsecutiveCalls([1], [2]));

		User::setActive($user);

		$filteredIds = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			ArticleSearch::instance(
				[
					'filter.active_user_access' => true,
					'list.limit' => 0
				]
			)->search()
		);

		$this->assertFalse(in_array(2, $filteredIds));

		$filteredIds = array_map(
			function ($categoryData)
			{
				return (int) $categoryData['id'];
			},
			ArticleSearch::instance(
				[
					'filter.active_user_access' => true,
					'list.limit' => 0
				]
			)->search()
		);

		$this->assertTrue(in_array(2, $filteredIds));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function directionIsApplied()
	{
		$ids = array_map(
			function ($itemData)
			{
				return (int) $itemData['id'];
			},
			ArticleSearch::instance(['list.limit' => 0])->search()
		);

		$this->assertNotSame(1, count($ids));
		$this->assertSame(20, $ids[0]);

		$reverseIds = array_map(
			function ($itemData)
			{
				return (int) $itemData['id'];
			},
			ArticleSearch::instance(['list.direction' => 'DESC', 'list.limit' => 0])->search()
		);

		$this->assertSame(count($ids), count($reverseIds));
		$this->assertSame(1, $reverseIds[0]);
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_content', JPATH_TESTS_PHPROBERTO . '/tests/Content/Stubs/Database/content.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function idFilterIsApplied()
	{
		$results = ArticleSearch::instance(['filter.id' => 17])->search();

		$this->assertSame(1, count($results));

		$this->assertSame(17, (int) $results[0]['id']);

		$results = ArticleSearch::instance(['filter.id' => [17, 18]])->search();

		$this->assertSame(2, count($results));

		$this->assertSame(18, (int) $results[0]['id']);
		$this->assertSame(17, (int) $results[1]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function notIdFilterIsApplied()
	{
		$articles = ArticleSearch::instance(['list.limit' => 2])->search();

		$this->assertSame(2, count($articles));

		$articlesNotId = ArticleSearch::instance(
			['filter.not_id' => $articles[0]['id'], 'list.limit' => 2]
		)->search();

		$this->assertSame(2, count($articles));

		$this->assertNotSame($articles[0]['id'], $articlesNotId[0]['id']);

		$articlesNotId = ArticleSearch::instance(
			['filter.not_id' => [$articles[0]['id'], $articles[1]['id']], 'list.limit' => 2]
		)->search();

		$this->assertSame(2, count($articles));

		$this->assertNotSame($articles[0]['id'], $articlesNotId[0]['id']);
		$this->assertNotSame($articles[1]['id'], $articlesNotId[1]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function orderingIsApplied()
	{
		$articles = ArticleSearch::instance()->search();

		$this->assertSame(20, (int) $articles[0]['id']);

		$articles = ArticleSearch::instance(
			[
				'list.ordering' => 'a.title'
			]
		)->search();

		$this->assertSame(1, (int) $articles[0]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchFilterIsApplied()
	{
		$articles = ArticleSearch::instance(['filter.search' => 'Banner', 'list.limit' => 0])->search();

		$this->assertSame(1, count($articles));
		$this->assertSame(7, (int) $articles[0]['id']);

		$articles = ArticleSearch::instance(['filter.search' => 'fruit-shop', 'list.limit' => 0])->search();

		$this->assertSame(1, count($articles));
		$this->assertSame(20, (int) $articles[0]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function stateFilterIsApplied()
	{
		$statuses = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['state'];
				},
				ArticleSearch::instance(
					[
						'filter.state' => 1,
						'list.limit'   => 0
					]
				)->search()
			)
		);

		$this->assertSame([1], $statuses);

		$unpublishedStatuses = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['state'];
				},
				ArticleSearch::instance(
					[
						'list.limit'   => 0,
						'filter.state' => 0
					]
				)->search()
			)
		);

		$this->assertSame([0], $unpublishedStatuses);

		$statuses = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['state'];
				},
				ArticleSearch::instance(
					[
						'list.limit'       => 0,
						'filter.state' => [0, 1]
					]
				)->search()
			)
		);

		$this->assertTrue(in_array(0, $statuses, true));
		$this->assertTrue(in_array(1, $statuses, true));

		$statuses = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['state'];
				},
				ArticleSearch::instance(
					[
						'list.limit' => 0,
						'filter.state' => null
					]
				)->search()
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

		User::clearActive();
	}
}
