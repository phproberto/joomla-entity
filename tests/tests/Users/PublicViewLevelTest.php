<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\PublicUserGroup;
use Phproberto\Joomla\Entity\Users\PublicViewLevel;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

/**
 * PublicViewLevel entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class PublicViewLevelTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructReturnsEntityIfExists()
	{
		PublicViewLevel::create();

		$viewLevel = new PublicViewLevel;

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
		$entity = new PublicViewLevel;
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCreatedEntity()
	{
		$this->assertTrue(PublicViewLevel::instanceOrCreate()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceOrCreateReturnsCachedInstance()
	{
		$cachedEntity = PublicViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = PublicViewLevel::instanceOrCreate();

		$this->assertInstanceOf(PublicViewLevel::class, $entity);

		$this->assertSame($cachedEntity, $entity);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$cachedEntity = PublicViewLevel::create();
		$cachedEntity->assign('title', 'edited');

		$entity = PublicViewLevel::instance();

		$this->assertInstanceOf(PublicViewLevel::class, $entity);
		$this->assertSame($cachedEntity, $entity);
		$this->assertSame($cachedEntity->get('title'), PublicViewLevel::instance()->get('title'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExistingEntity()
	{
		PublicViewLevel::create();

		$this->assertTrue(PublicViewLevel::instance()->isLoaded());
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
		$this->assertTrue(PublicViewLevel::instance()->isLoaded());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function userGroupsReturnsExpectedGroups()
	{
		PublicViewLevel::create();

		$expected = [
			PublicUserGroup::instance()->id()
		];

		$groups = PublicViewLevel::instance()->userGroups();

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

		PublicViewLevel::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}
}
