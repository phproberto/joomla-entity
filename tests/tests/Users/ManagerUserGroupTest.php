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
use Phproberto\Joomla\Entity\Users\ManagerUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * ManagerUserGroup entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ManagerUserGroupTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnGroupIfExists()
	{
		ManagerUserGroup::create();

		$group = new ManagerUserGroup;

		$this->assertInstanceOf(ManagerUserGroup::class, $group);
		$this->assertTrue($group->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedGroup()
	{
		$this->assertTrue(ManagerUserGroup::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedGroup = ManagerUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = ManagerUserGroup::instanceOrCreate();

		$this->assertInstanceOf(ManagerUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedGroup = ManagerUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = ManagerUserGroup::instance();

		$this->assertInstanceOf(ManagerUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
		$this->assertSame($cachedGroup->get('title'), ManagerUserGroup::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingGroup()
	{
		ManagerUserGroup::create();

		$group = ManagerUserGroup::instance();

		$this->assertInstanceOf(ManagerUserGroup::class, $group);
		$this->assertTrue($group->isLoaded());
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
		$this->assertTrue(ManagerUserGroup::instance()->isLoaded());
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
		$group = new ManagerUserGroup;
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

		ManagerUserGroup::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
