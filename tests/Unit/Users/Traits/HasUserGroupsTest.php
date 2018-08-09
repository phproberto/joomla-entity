<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\UserGroup;
use Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs\EntityWithUserGroups;

/**
 * HasUserGroups trait tests.
 *
 * @since   1.1.0
 */
class HasUserGroupsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * clearUserGroups clears userGroups property.
	 *
	 * @return  void
	 */
	public function testClearUserGroupsClearsUserGroupsProperty()
	{
		$entity = new EntityWithUserGroups;

		$reflection = new \ReflectionClass($entity);

		$userGroupsProperty = $reflection->getProperty('userGroups');
		$userGroupsProperty->setAccessible(true);

		$this->assertSame(null, $userGroupsProperty->getValue($entity));

		$userGroups = new Collection(array(UserGroup::find(333)));

		$userGroupsProperty->setValue($entity, $userGroups);

		$this->assertSame($userGroups, $userGroupsProperty->getValue($entity));

		$entity->clearUserGroups();

		$this->assertSame(null, $userGroupsProperty->getValue($entity));
	}

	/**
	 * userGroups returns cached data.
	 *
	 * @return  void
	 */
	public function testUserGroupsReturnsCachedData()
	{
		$entity = new EntityWithUserGroups;

		$reflection = new \ReflectionClass($entity);

		$userGroupsProperty = $reflection->getProperty('userGroups');
		$userGroupsProperty->setAccessible(true);

		$this->assertSame(null, $userGroupsProperty->getValue($entity));

		$userGroups = new Collection(array(UserGroup::find(333)));

		$userGroupsProperty->setValue($entity, $userGroups);

		$this->assertSame($userGroups, $entity->userGroups());
	}

	/**
	 * userGroups returns loadUserGroups result if not cached.
	 *
	 * @return  void
	 */
	public function testUserGroupsReturnsLoadUserGroupsResultIfNotCached()
	{
		$userGroups = new Collection(
			array(
				UserGroup::find(333),
				UserGroup::find(666)
			)
		);

		$entity = $this->getMockBuilder(EntityWithUserGroups::class)
			->setMethods(array('loadUserGroups'))
			->getMock();

		$entity->expects($this->once())
			->method('loadUserGroups')
			->willReturn($userGroups);

		$this->assertSame($userGroups, $entity->userGroups());
	}

	/**
	 * hasUserGroup returns correct value.
	 *
	 * @return  void
	 */
	public function testHasUserGroupReturnsCorrectValue()
	{
		$userGroups = new Collection(
			array(
				UserGroup::find(666),
				UserGroup::find(999)
			)
		);

		$entity = new EntityWithUserGroups;
		$reflection = new \ReflectionClass($entity);

		$userGroupsProperty = $reflection->getProperty('userGroups');
		$userGroupsProperty->setAccessible(true);
		$userGroupsProperty->setValue($entity, $userGroups);

		$this->assertTrue($entity->hasUserGroup(666));
		$this->assertFalse($entity->hasUserGroup(333));
		$this->assertTrue($entity->hasUserGroup(999));
	}

	/**
	 * hasUserGroups returns correct value.
	 *
	 * @return  void
	 */
	public function testHasUserGroupsReturnsCorrectValue()
	{
		$userGroups = new Collection(
			array(
				UserGroup::find(333),
				UserGroup::find(666)
			)
		);

		$entity = new EntityWithUserGroups;
		$reflection = new \ReflectionClass($entity);

		$userGroupsProperty = $reflection->getProperty('userGroups');
		$userGroupsProperty->setAccessible(true);
		$userGroupsProperty->setValue($entity, $userGroups);

		$this->assertTrue($entity->hasUserGroups());

		$userGroupsProperty->setValue($entity, new Collection);

		$this->assertFalse($entity->hasUserGroups());
	}
}
