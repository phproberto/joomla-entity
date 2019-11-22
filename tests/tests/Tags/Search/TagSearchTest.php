<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags\Search;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tags\Search\TagSearch;

/**
 * Tag search tests.
 *
 * @since   1.7.0
 */
class TagSearchTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function activeLanguageFilterIsApplied()
	{
		$tags = TagSearch::instance(
			[
				'filter.active_language' => true,
				'list.limit' => 0
			]
		)->searchFresh();

		$this->assertNotSame(0, count($tags));

		foreach ($tags as $category)
		{
			$this->assertSame('en-GB', $category['language']);
		}
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
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(
				[
					'filter.active_user_access' => true,
					'list.limit' => 0
				]
			)->searchFresh()
		);

		$this->assertFalse(in_array(7, $filteredIds));

		$filteredIds = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(
				[
					'filter.active_user_access' => true,
					'list.limit' => 0
				]
			)->searchFresh()
		);

		$this->assertTrue(in_array(7, $filteredIds));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function accessFilterIsApplied()
	{
		$ids = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(
				[
					'filter.access' => 1,
					'list.limit' => 0
				]
			)->searchFresh()
		);

		$this->assertFalse(in_array(7, $ids));

		$filteredIds = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(
				[
					'filter.access' => 2,
					'list.limit' => 0
				]
			)->searchFresh()
		);

		$this->assertTrue(in_array(7, $filteredIds));

		$multipleFilteredIds = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(['filter.access' => [1, 2], 'list.limit' => 0])->searchFresh()
		);

		$this->assertSame($ids, array_intersect($ids, $multipleFilteredIds));
		$this->assertSame($filteredIds, array_intersect($filteredIds, $multipleFilteredIds));
		$this->assertSame(count($multipleFilteredIds), count($ids) + count($filteredIds));

		$nullAccessIds = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(['filter.access' => null, 'list.limit' => 0])->searchFresh()
		);

		$this->assertSame($multipleFilteredIds, $nullAccessIds);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function contentItemIdFilterIsApplied()
	{
		$tags = TagSearch::instance(
			[
				'filter.content_item_id' => 24,
				'list.limit' => 0
			]
		)->searchFresh();

		$this->assertSame(4, count($tags));

		$this->assertSame('2', $tags[0]['id']);
		$this->assertSame('3', $tags[1]['id']);
		$this->assertSame('4', $tags[2]['id']);
		$this->assertSame('5', $tags[3]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function contentTypeAliasFilterIsApplied()
	{
		$tags = TagSearch::instance(
			[
				'filter.content_type_alias' => 'com_content.category',
				'list.limit' => 0
			]
		)->searchFresh();

		$this->assertSame(3, count($tags));

		$this->assertSame('4', $tags[0]['id']);
		$this->assertSame('6', $tags[1]['id']);
		$this->assertSame('7', $tags[2]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function descendantFilterIsApplied()
	{
		$ids = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(['filter.descendant_id' => 7, 'list.limit' => 0])->searchFresh()
		);

		$this->assertSame([1,8,6], $ids);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ancestorFilterIsApplied()
	{
		$ids = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(['filter.ancestor_id' => 20, 'list.limit' => 0])->searchFresh()
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
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(['list.limit' => 0])->searchFresh()
		);

		$this->assertNotSame(1, count($ids));
		$this->assertSame(1, $ids[0]);

		$reverseIds = array_map(
			function ($tagData)
			{
				return (int) $tagData['id'];
			},
			TagSearch::instance(['list.direction' => 'DESC', 'list.limit' => 0])->searchFresh()
		);

		$this->assertSame(count($ids), count($reverseIds));
		$this->assertSame(1, end($reverseIds));
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_tags', dirname(__DIR__) . '/Stubs/Database/tags.csv');
		$dataSet->addTable('jos_contentitem_tag_map', dirname(__DIR__) . '/Stubs/Database/contentitem_tag_map.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function idFilterIsApplied()
	{
		$results = TagSearch::instance(['filter.id' => 3])->searchFresh();

		$this->assertSame(1, count($results));

		$this->assertSame(3, (int) $results[0]['id']);

		$results = TagSearch::instance(['filter.id' => [3, 4]])->searchFresh();

		$this->assertSame(2, count($results));

		$this->assertSame(3, (int) $results[0]['id']);
		$this->assertSame(4, (int) $results[1]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function languageFilterIsApplied()
	{
		$items = TagSearch::instance(['filter.language' => 'en-GB'])->searchFresh();

		$this->assertNotSame(0, count($items));

		foreach ($items as $item)
		{
			$this->assertSame('en-GB', $item['language']);
		}
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function levelFilterIsApplied()
	{
		$tags = TagSearch::instance(['filter.level' => 3, 'list.limit' => 2])->searchFresh();

		$this->assertSame(2, count($tags));

		foreach ($tags as $category)
		{
			$this->assertSame(3, (int) $category['level']);
		}
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function notIdFilterIsApplied()
	{
		$tags = TagSearch::instance(['list.limit' => 2])->searchFresh();

		$this->assertSame(2, count($tags));

		$tagsNotId = TagSearch::instance(
			['filter.not_id' => $tags[0]['id'], 'list.limit' => 2]
		)->searchFresh();

		$this->assertSame(2, count($tags));

		$this->assertNotSame($tags[0]['id'], $tagsNotId[0]['id']);

		$tagsNotId = TagSearch::instance(
			['filter.not_id' => [$tags[0]['id'], $tags[1]['id']], 'list.limit' => 2]
		)->searchFresh();

		$this->assertSame(2, count($tags));

		$this->assertNotSame($tags[0]['id'], $tagsNotId[0]['id']);
		$this->assertNotSame($tags[1]['id'], $tagsNotId[1]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function orderingIsApplied()
	{
		$tags = TagSearch::instance()->searchFresh();

		$this->assertSame(1, (int) $tags[0]['id']);

		$tags = TagSearch::instance(
			[
				'filter.published' => null,
				'list.ordering'    => 't.published'
			]
		)->searchFresh();

		$this->assertSame(5, (int) $tags[0]['id']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentFilterIsApplied()
	{
		$tags = TagSearch::instance(['filter.parent_id' => 6, 'list.limit' => 5])->searchFresh();

		$this->assertSame(2, count($tags));

		foreach ($tags as $tagData)
		{
			$this->assertSame(6, (int) $tagData['parent_id']);
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
				function ($tagData)
				{
					return (int) $tagData['published'];
				},
				TagSearch::instance(
					[
						'filter.published' => 1,
						'list.limit' => 0
					]
				)->searchFresh()
			)
		);

		$this->assertSame([1], $statuses);

		$unpublishedStatuses = array_unique(
			array_map(
				function ($tagData)
				{
					return (int) $tagData['published'];
				},
				TagSearch::instance(['list.limit' => 0, 'filter.published' => 0])->searchFresh()
			)
		);

		$this->assertSame([0], $unpublishedStatuses);

		$statuses = array_unique(
			array_map(
				function ($tagData)
				{
					return (int) $tagData['published'];
				},
				TagSearch::instance(['list.limit' => 0, 'filter.published' => [0, 1]])->searchFresh()
			)
		);

		$this->assertTrue(in_array(0, $statuses, true));
		$this->assertTrue(in_array(1, $statuses, true));

		$statuses = array_unique(
			array_map(
				function ($tagData)
				{
					return (int) $tagData['published'];
				},
				TagSearch::instance(['list.limit' => 0, 'filter.published' => null])->searchFresh()
			)
		);

		$this->assertTrue(in_array(0, $statuses, true));
		$this->assertTrue(in_array(1, $statuses, true));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchFilterIsApplied()
	{
		$tags = TagSearch::instance(['filter.search' => 'diesel-alias', 'list.limit' => 0])->searchFresh();

		$this->assertSame(1, count($tags));
		$this->assertSame(7, (int) $tags[0]['id']);

		$tags = TagSearch::instance(['filter.search' => 'cars/gasoline', 'list.limit' => 0])->searchFresh();

		$this->assertSame(1, count($tags));
		$this->assertSame(9, (int) $tags[0]['id']);

		$tags = TagSearch::instance(['filter.search' => 'Vehicles title', 'list.limit' => 0])->searchFresh();

		$this->assertSame(1, count($tags));

		$this->assertSame(8, (int) $tags[0]['id']);
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
