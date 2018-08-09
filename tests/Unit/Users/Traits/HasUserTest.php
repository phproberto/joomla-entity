<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs\EntityWithUser;

/**
 * HasUser trait tests.
 *
 * @since   1.1.0
 */
class HasUserTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Column to use to load/store user.
	 *
	 * @const
	 */
	const COLUMN_USER = 'userid';

	/**
	 * Get a mocked entity.
	 *
	 * @param   array  $row  Row returned by the entity as data
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getEntity($row = array())
	{
		$entity = $this->getMockBuilder(EntityWithUser::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::COLUMN_USER);

		$entity->bind($row);

		return $entity;
	}

	/**
	 * user returns cached data.
	 *
	 * @return  void
	 */
	public function testUserReturnsCachedData()
	{
		$entity = new EntityWithUser;

		$reflection = new \ReflectionClass($entity);

		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertSame(null, $userProperty->getValue($entity));

		$user = new User(666);
		$userProperty->setValue($entity, $user);

		$this->assertSame($user, $entity->user());
	}

	/**
	 * user returns loadUser result when not cached.
	 *
	 * @return  void
	 */
	public function testUserReturnsLoadUserResultWhenNotCached()
	{
		$user = new User(999);

		$entity = $this->getMockBuilder(EntityWithUser::class)
			->setMethods(array('loadUser'))
			->getMock();

		$entity->expects($this->once())
			->method('loadUser')
			->willReturn($user);

		$reflection = new \ReflectionClass($entity);

		$userProperty = $reflection->getProperty('user');
		$userProperty->setAccessible(true);

		$this->assertSame(null, $userProperty->getValue($entity));
		$this->assertSame($user, $entity->user());
	}

	/**
	 * HasUser returns false for empty entity.
	 *
	 * @return  void
	 */
	public function testHasUserReturnsFalseForEmptyEntity()
	{
		$entity = $this->getEntity();

		$this->assertFalse($entity->hasUser());
	}

	/**
	 * hasUser returns true for entities with user id.
	 *
	 * @return  void
	 */
	public function testHasUserReturnsTrueForEntitiesWithUserId()
	{
		$entity = $this->getEntity(
			array(
				'id' => 23,
				self::COLUMN_USER => 666
			)
		);

		$this->assertTrue($entity->hasUser());
	}

	/**
	 * loadUser returns correct value for entities with user id.
	 *
	 * @return  void
	 */
	public function testLoadUserReturnsCorrectValueForEntitiesWithUserId()
	{
		$entity = $this->getEntity(
			array(
				'id' => 23,
				self::COLUMN_USER => 666
			)
		);

		$reflection = new \ReflectionClass($entity);

		$method = $reflection->getMethod('loadUser');
		$method->setAccessible(true);

		$this->assertEquals(User::find(666), $method->invoke($entity));
	}
}
