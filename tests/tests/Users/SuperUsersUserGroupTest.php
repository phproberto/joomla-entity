<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\UserGroup;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * SuperUsersUserGroup entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class SuperUsersUserGroupTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnGroupIfExists()
	{
		SuperUsersUserGroup::create();

		$group = new SuperUsersUserGroup;

		$this->assertTrue($group->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedGroup()
	{
		$this->assertTrue(SuperUsersUserGroup::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedGroup = SuperUsersUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = SuperUsersUserGroup::instanceOrCreate();

		$this->assertInstanceOf(SuperUsersUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedGroup = SuperUsersUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = SuperUsersUserGroup::instance();

		$this->assertInstanceOf(SuperUsersUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
		$this->assertSame($cachedGroup->get('title'), SuperUsersUserGroup::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingGroup()
	{
		SuperUsersUserGroup::create();

		$this->assertTrue(SuperUsersUserGroup::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function instanceThrowsExceptionForUnexistingGroup()
	{
		$this->assertTrue(SuperUsersUserGroup::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function groupThrowsExceptionIfGroupDoesNotExist()
	{
		$group = new SuperUsersUserGroup;
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
		EmptyTable::instance(['#__usergroups'])->execute();

		SuperUsersUserGroup::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
