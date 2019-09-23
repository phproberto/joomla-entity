<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Users\SuperUsersViewLevel;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * SuperUsersViewLevel entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class SuperUsersViewLevelTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnsEntityIfExists()
	{
		SuperUsersViewLevel::create();

		$viewLevel = new SuperUsersViewLevel;

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
		$entity = new SuperUsersViewLevel;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedEntity()
	{
		$this->assertTrue(SuperUsersViewLevel::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedEntity = SuperUsersViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = SuperUsersViewLevel::instanceOrCreate();

		$this->assertInstanceOf(SuperUsersViewLevel::class, $entity);

		$this->assertSame($cachedEntity, $entity);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedEntity = SuperUsersViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = SuperUsersViewLevel::instance();

		$this->assertInstanceOf(SuperUsersViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
		$this->assertSame($cachedEntity->get('title'), SuperUsersViewLevel::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingEntity()
	{
		SuperUsersViewLevel::create();

		$this->assertTrue(SuperUsersViewLevel::instance()->isLoaded());
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
		$this->assertTrue(SuperUsersViewLevel::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsExpectedGroups()
	{
		SuperUsersViewLevel::create();

		$expected = [
			SuperUsersUserGroup::instance()->id()
		];

		$groups = SuperUsersViewLevel::instance()->userGroups();

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

		SuperUsersViewLevel::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
