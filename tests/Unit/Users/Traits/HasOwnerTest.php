<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Column;
use Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs\EntityWithOwner;

/**
 * HasOwner trait tests.
 *
 * @since   1.1.0
 */
class HasOwnerTest extends \TestCaseDatabase
{
	/**
	 * Name of the owner column.
	 *
	 * @const
	 */
	const OWNER_COLUMN = 'created_by';

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

		\JFactory::$session     = $this->getMockSession();
		\JFactory::$config      = $this->getMockConfig();
		\JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithOwner::clearAll();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * hasOwner returns correct value.
	 *
	 * @return  void
	 */
	public function testHasOwnerReturnsCorrectValue()
	{
		$class = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->expects($this->once())
			->method('columnAlias')
			->willReturn(static::OWNER_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999, static::OWNER_COLUMN => 22));

		$this->assertSame(true, $class->hasOwner());
	}

	/**
	 * hasOwner throws exception for missing owner column.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testHasOwnerThrowsExceptionFormissingOwnerColumn()
	{
		$class = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->expects($this->once())
			->method('columnAlias')
			->willReturn(static::OWNER_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999));

		$this->assertSame(true, $class->hasOwner());
	}

	/**
	 * isOwner returns false for guest.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsFalseForGuest()
	{
		$user = $this->getMockBuilder(User::class)
			->disableOriginalConstructor()
			->setMethods(array('isGuest'))
			->getMock();

		$user->expects($this->once())
			->method('isGuest')
			->willReturn(true);

		$entity = new EntityWithOwner;

		$this->assertFalse($entity->isOwner($user));
	}

	/**
	 * isOwner works with no user.
	 *
	 * @return  void
	 */
	public function testIsOwnerWorksWithNoUser()
	{
		$entity = new EntityWithOwner;

		$this->assertFalse($entity->isOwner());
	}

	/**
	 * isOwner returns false if user is not owner.
	 *
	 * @return  void
	 */
	public function testIsOwnerReturnsCorrectValueForNonGuests()
	{
		$entity = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::OWNER_COLUMN);

		$user = $this->getMockBuilder(User::class)
			->disableOriginalConstructor()
			->setMethods(array('isGuest', 'id'))
			->getMock();

		$user->method('isGuest')
			->willReturn(false);

		$user->method('id')
			->will($this->onConsecutiveCalls(333, 666));

		$entity->bind(array('id' => 999, static::OWNER_COLUMN => 666));

		$this->assertFalse($entity->isOwner($user));
		$this->assertTrue($entity->isOwner($user));
	}

	/**
	 * loadOwner returns correct user.
	 *
	 * @return  void
	 */
	public function testLoadOwnerReturnsCorrectUser()
	{
		$class = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->expects($this->once())
			->method('columnAlias')
			->willReturn(static::OWNER_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999, static::OWNER_COLUMN => 22));

		$method = $reflection->getMethod('loadOwner');
		$method->setAccessible(true);

		$this->assertSame(User::find(22), $method->invoke($class));
	}

	/**
	 * loadOwner throws exception when entity does not have owner.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testLoadOwnerThrowsExceptionWhenEntityDoesNotHaveOwner()
	{
		$entity = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::OWNER_COLUMN);

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999, static::OWNER_COLUMN => null));

		$method = $reflection->getMethod('loadOwner');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * loadOwner throws exception for missing owner column.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testLoadOwnerThrowsExceptionForMissingOwnerColumn()
	{
		$entity = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::OWNER_COLUMN);

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999));

		$method = $reflection->getMethod('loadOwner');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * owner calls loadOwner.
	 *
	 * @return  void
	 */
	public function testOwnerCallsLoadOwner()
	{
		$owner = new User(24);

		$class = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('loadOwner'))
			->getMock();

		$class->expects($this->once())
			->method('loadOwner')
			->willReturn($owner);

		$this->assertSame($owner, $class->owner());
	}

	/**
	 * owner returns cached instance.
	 *
	 * @return  void
	 */
	public function testOwnerReturnsCachedInstance()
	{
		$owner = new User(999);

		$class = new EntityWithOwner;

		$reflection = new \ReflectionClass($class);

		$authorProperty = $reflection->getProperty('owner');
		$authorProperty->setAccessible(true);
		$authorProperty->setValue($class, $owner);

		$this->assertSame($owner, $class->owner());
	}

	/**
	 * owner reloads data.
	 *
	 * @return  void
	 */
	public function testOwnerReloadsData()
	{
		$owner = new User(24);
		$reloadedOwner = new User(999);

		$class = $this->getMockBuilder(EntityWithOwner::class)
			->disableOriginalConstructor()
			->setMethods(array('loadOwner'))
			->getMock();

		$class
			->method('loadOwner')
			->will($this->onConsecutiveCalls($owner, $reloadedOwner));

		$this->assertSame($owner, $class->owner());
		$this->assertSame($reloadedOwner, $class->owner(true));
		$this->assertSame($reloadedOwner, $class->owner());
	}
}
