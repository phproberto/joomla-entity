<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\UserGroup;
use Phproberto\Joomla\Entity\Users\GuestUserGroup;
use Phproberto\Joomla\Entity\Users\PublicUserGroup;
use Phproberto\Joomla\Entity\Users\RegisteredUserGroup;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;
use Phproberto\Joomla\Entity\Users\Command\CreatePredefinedUserGroups;

/**
 * CreatePredefinedUserGroups tests.
 *
 * @since   __DEPOY_VERSION__
 */
class CreatePredefinedUserGroupsTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function groupsAreCreated()
	{
		$groups = CreatePredefinedUserGroups::instance()->execute();

		$this->assertTrue(count($groups) > 0);

		foreach ($groups as $key => $group)
		{
			$this->assertInstanceOf(UserGroup::class, $group);
		}
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

		Factory::$config      = $this->getMockConfig();
		Factory::$session     = $this->getMockSession();
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

		$this->restoreFactoryState();

		PublicUserGroup::clearAll();
		RegisteredUserGroup::clearAll();
		GuestUserGroup::clearAll();
		SuperUsersUserGroup::clearAll();

		parent::tearDown();
	}
}
