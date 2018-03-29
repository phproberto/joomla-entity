<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs\EntityWithUsers;

/**
 * HasUsers trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasUsersTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * clearUsers clears users property.
	 *
	 * @return  void
	 */
	public function testClearUsersClearsUsersProperty()
	{
		$entity = new EntityWithUsers;

		$reflection = new \ReflectionClass($entity);

		$usersProperty = $reflection->getProperty('users');
		$usersProperty->setAccessible(true);

		$this->assertSame(null, $usersProperty->getValue($entity));

		$users = new Collection(array(User::find(333)));

		$usersProperty->setValue($entity, $users);

		$this->assertSame($users, $usersProperty->getValue($entity));

		$entity->clearUsers();

		$this->assertSame(null, $usersProperty->getValue($entity));
	}

	/**
	 * users returns cached data.
	 *
	 * @return  void
	 */
	public function testUsersReturnsCachedData()
	{
		$entity = new EntityWithUsers;

		$reflection = new \ReflectionClass($entity);

		$usersProperty = $reflection->getProperty('users');
		$usersProperty->setAccessible(true);

		$this->assertSame(null, $usersProperty->getValue($entity));

		$users = new Collection(array(User::find(333)));

		$usersProperty->setValue($entity, $users);

		$this->assertSame($users, $entity->users());
	}

	/**
	 * users returns loadUsers result if not cached.
	 *
	 * @return  void
	 */
	public function testUsersReturnsLoadUsersResultIfNotCached()
	{
		$users = new Collection(
			array(
				User::find(333),
				User::find(666)
			)
		);

		$entity = $this->getMockBuilder(EntityWithUsers::class)
			->setMethods(array('loadUsers'))
			->getMock();

		$entity->expects($this->once())
			->method('loadUsers')
			->willReturn($users);

		$this->assertSame($users, $entity->users());
	}

	/**
	 * hasUser returns correct value.
	 *
	 * @return  void
	 */
	public function testHasUserReturnsCorrectValue()
	{
		$users = new Collection(
			array(
				User::find(666),
				User::find(999)
			)
		);

		$entity = new EntityWithUsers;
		$reflection = new \ReflectionClass($entity);

		$usersProperty = $reflection->getProperty('users');
		$usersProperty->setAccessible(true);
		$usersProperty->setValue($entity, $users);

		$this->assertTrue($entity->hasUser(666));
		$this->assertFalse($entity->hasUser(333));
		$this->assertTrue($entity->hasUser(999));
	}

	/**
	 * hasUsers returns correct value.
	 *
	 * @return  void
	 */
	public function testHasUsersReturnsCorrectValue()
	{
		$users = new Collection(
			array(
				User::find(333),
				User::find(666)
			)
		);

		$entity = new EntityWithUsers;
		$reflection = new \ReflectionClass($entity);

		$usersProperty = $reflection->getProperty('users');
		$usersProperty->setAccessible(true);
		$usersProperty->setValue($entity, $users);

		$this->assertTrue($entity->hasUsers());

		$usersProperty->setValue($entity, new Collection);

		$this->assertFalse($entity->hasUsers());
	}
}
