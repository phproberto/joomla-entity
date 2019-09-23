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
use Phproberto\Joomla\Entity\Users\AuthorUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * AuthorUserGroup entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class AuthorUserGroupTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnGroupIfExists()
	{
		AuthorUserGroup::create();

		$group = new AuthorUserGroup;

		$this->assertInstanceOf(AuthorUserGroup::class, $group);
		$this->assertTrue($group->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedGroup()
	{
		$this->assertTrue(AuthorUserGroup::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedGroup = AuthorUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = AuthorUserGroup::instanceOrCreate();

		$this->assertInstanceOf(AuthorUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedGroup = AuthorUserGroup::create();
		$cachedGroup->assign('title', 'edited');

		$group = AuthorUserGroup::instance();

		$this->assertInstanceOf(AuthorUserGroup::class, $group);
		$this->assertSame($cachedGroup, $group);
		$this->assertSame($cachedGroup->get('title'), AuthorUserGroup::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingGroup()
	{
		AuthorUserGroup::create();

		$group = AuthorUserGroup::instance();

		$this->assertInstanceOf(AuthorUserGroup::class, $group);
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
		$this->assertTrue(AuthorUserGroup::instance()->isLoaded());
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
		$group = new AuthorUserGroup;
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

		AuthorUserGroup::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
