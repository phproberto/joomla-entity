<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\AuthorUserGroup;
use Phproberto\Joomla\Entity\Users\ManagerUserGroup;
use Phproberto\Joomla\Entity\Users\SpecialViewLevel;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * SpecialViewLevel entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class SpecialViewLevelTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnsEntityIfExists()
	{
		SpecialViewLevel::create();

		$viewLevel = new SpecialViewLevel;

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
		$entity = new SpecialViewLevel;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedEntity()
	{
		$this->assertTrue(SpecialViewLevel::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedEntity = SpecialViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = SpecialViewLevel::instanceOrCreate();

		$this->assertInstanceOf(SpecialViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedEntity = SpecialViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = SpecialViewLevel::instance();

		$this->assertInstanceOf(SpecialViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
		$this->assertSame($cachedEntity->get('title'), SpecialViewLevel::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingEntity()
	{
		SpecialViewLevel::create();

		$this->assertTrue(SpecialViewLevel::instance()->isLoaded());
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
		$this->assertTrue(SpecialViewLevel::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsExpectedGroups()
	{
		SpecialViewLevel::create();

		$expected = [
			AuthorUserGroup::instance()->id(),
			ManagerUserGroup::instance()->id(),
			SuperUsersUserGroup::instance()->id()
		];

		$groups = SpecialViewLevel::instance()->userGroups();

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

		SpecialViewLevel::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
