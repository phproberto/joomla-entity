<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\GuestUserGroup;
use Phproberto\Joomla\Entity\Users\GuestViewLevel;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * GuestViewLevel entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class GuestViewLevelTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnsEntityIfExists()
	{
		GuestViewLevel::create();

		$viewLevel = new GuestViewLevel;

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
		$entity = new GuestViewLevel;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedEntity()
	{
		$this->assertTrue(GuestViewLevel::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedEntity = GuestViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = GuestViewLevel::instanceOrCreate();

		$this->assertInstanceOf(GuestViewLevel::class, $entity);

		$this->assertSame($cachedEntity, $entity);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedEntity = GuestViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = GuestViewLevel::instance();

		$this->assertInstanceOf(GuestViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
		$this->assertSame($cachedEntity->get('title'), GuestViewLevel::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingEntity()
	{
		GuestViewLevel::create();

		$this->assertTrue(GuestViewLevel::instance()->isLoaded());
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
		$this->assertTrue(GuestViewLevel::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsExpectedGroups()
	{
		GuestViewLevel::create();

		$expected = [
			GuestUserGroup::instance()->id()
		];

		$groups = GuestViewLevel::instance()->userGroups();

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

		GuestViewLevel::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
