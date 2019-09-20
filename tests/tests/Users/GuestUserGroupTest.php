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
use Phproberto\Joomla\Entity\Users\GuestUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * GuestUserGroup entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class GuestUserGroupTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnGroupIfExists()
	{
		GuestUserGroup::create();

		$group = new GuestUserGroup;

		$this->assertTrue($group->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedGroup()
	{
		$this->assertTrue(GuestUserGroup::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedGroup = GuestUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = GuestUserGroup::instanceOrCreate();

		$this->assertInstanceOf(GuestUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedGroup = GuestUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = GuestUserGroup::instance();

		$this->assertInstanceOf(GuestUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
		$this->assertSame($cachedGroup->get('title'), GuestUserGroup::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingGroup()
	{
		GuestUserGroup::create();

		$this->assertTrue(GuestUserGroup::instance()->isLoaded());
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
		$this->assertTrue(GuestUserGroup::instance()->isLoaded());
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
		$group = new GuestUserGroup;
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

		GuestUserGroup::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
