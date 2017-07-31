<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Users\User;

/**
 * User entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class UserTest extends \TestCaseDatabase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		User::clearAllInstances();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * entity instance can be retrieved.
	 *
	 * @return  void
	 */
	public function testEntityInstanceRetrieved()
	{
		$user = new User;

		$this->assertInstanceOf(User::class, $user);

		$user = new User(12);

		$this->assertInstanceOf(User::class, $user);
	}

	/**
	 * params can be retrieved.
	 *
	 * @return  void
	 */
	public function testParamsRetrieved()
	{
		$user = new User(999);

		$reflection = new \ReflectionClass($user);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($user, array('id' => 999));

		$this->assertEquals(new Registry, $user->params());

		$rowProperty->setValue($user, array('id' => 999, 'params' => '{"timezone":"Europe\/Madrid"}'));

		$this->assertEquals(new Registry(array('timezone' => 'Europe/Madrid')), $user->params(true));
	}
}
