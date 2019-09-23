<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\ManagerUserGroup;
use Phproberto\Joomla\Entity\Users\RegisteredUserGroup;
use Phproberto\Joomla\Entity\Users\RegisteredViewLevel;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * RegisteredViewLevel entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class RegisteredViewLevelTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnsEntityIfExists()
	{
		RegisteredViewLevel::create();

		$viewLevel = new RegisteredViewLevel;

		$this->assertTrue($viewLevel->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function constructorThrowsExceptionIfEntityDoesNotExist()
	{
		$entity = new RegisteredViewLevel;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedEntity()
	{
		$this->assertTrue(RegisteredViewLevel::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedEntity = RegisteredViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = RegisteredViewLevel::instanceOrCreate();

		$this->assertInstanceOf(RegisteredViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedEntity = RegisteredViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = RegisteredViewLevel::instance();

		$this->assertInstanceOf(RegisteredViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
		$this->assertSame($cachedEntity->get('title'), RegisteredViewLevel::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingEntity()
	{
		RegisteredViewLevel::create();

		$this->assertTrue(RegisteredViewLevel::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function instanceThrowsExceptionForUnexistingEntity()
	{
		$this->assertTrue(RegisteredViewLevel::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsExpectedGroups()
	{
		RegisteredViewLevel::create();

		$expected = [
			RegisteredUserGroup::instance()->id(),
			ManagerUserGroup::instance()->id(),
			SuperUsersUserGroup::instance()->id()
		];

		$groups = RegisteredViewLevel::instance()->userGroups();

		$this->assertEquals($expected, $groups->ids());
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
		EmptyTable::instance(['#__viewlevels'])->execute();

		RegisteredViewLevel::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
