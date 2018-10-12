<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Search;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Search\UserSearch;

/**
 * UserSearch tests.
 *
 * @since   __DEPLOY_VERSION_
 */
class UserSearchTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function blockedFilterIsApplied()
	{
		$statuses = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['block'];
				},
				UserSearch::instance(
					[
						'filter.blocked' => 0,
						'list.limit'   => 0
					]
				)->search()
			)
		);

		$this->assertSame([0], $statuses);

		$statuses = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['block'];
				},
				UserSearch::instance(
					[
						'filter.blocked' => 1,
						'list.limit'   => 0
					]
				)->search()
			)
		);

		$this->assertSame([1], $statuses);
	}
	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_users', __DIR__ . '/Stubs/Database/users.csv');
		$dataSet->addTable('jos_user_usergroup_map',  __DIR__ . '/Stubs/Database/user_usergroup_map.csv');

		return $dataSet;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function groupFilterIsApplied()
	{
		$users = UserSearch::instance(
			['filter.group' => 5, 'list.limit' => 0]
		)->search();

		$this->assertSame(1, count($users));
		$this->assertSame(816, (int) $users[0]['id']);

		$userIds = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['id'];
				},
				UserSearch::instance(
					['filter.group' => [6,8], 'list.limit' => 0]
				)->search()
			)
		);

		$this->assertTrue(in_array(815, $userIds, true));
		$this->assertTrue(in_array(817, $userIds, true));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function notGroupFilterIsApplied()
	{
		$users = UserSearch::instance(
			['filter.not_group' => 5, 'list.limit' => 0]
		)->search();

		$this->assertSame(2, count($users));
		$this->assertSame(817, (int) $users[0]['id']);
		$this->assertSame(815, (int) $users[1]['id']);

		$userIds = array_unique(
			array_map(
				function ($itemData)
				{
					return (int) $itemData['id'];
				},
				UserSearch::instance(
					['filter.not_group' => [6,8], 'list.limit' => 0]
				)->search()
			)
		);

		$this->assertSame(1, count($userIds));
		$this->assertTrue(in_array(816, $userIds, true));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function searchFilterIsApplied()
	{
		$users = UserSearch::instance(
			['filter.search' => 'Super User', 'list.limit' => 0]
		)->search();

		$this->assertSame(1, count($users));
		$this->assertSame(815, (int) $users[0]['id']);

		$users = UserSearch::instance(
			['filter.search' => 'my-name', 'list.limit' => 0]
		)->search();

		$this->assertSame(1, count($users));
		$this->assertSame(817, (int) $users[0]['id']);

		$users = UserSearch::instance(
			['filter.search' => 'me+sample', 'list.limit' => 0]
		)->search();

		$this->assertSame(1, count($users));
		$this->assertSame(816, (int) $users[0]['id']);
	}
}
