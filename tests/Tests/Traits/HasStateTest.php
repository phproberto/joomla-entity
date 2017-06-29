<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\EntityWithState;

/**
 * HasState trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasStateTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getColumnState returns table value.
	 *
	 * @return  void
	 */
	public function testgetColumnStateReturnsTableValue()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('getColumnAlias'))
			->getMock();

		$tableMock->expects($this->once())
			->method('getColumnAlias')
			->willReturn('state');

		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('getTable'))
			->getMock();

		$entity->method('getTable')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('getColumnState');
		$method->setAccessible(true);

		$this->assertSame('state', $method->invoke($entity));
	}

	/**
	 * getState throws RuntimeException for missing state column.
	 *
	 * @return  void
	 *
	 @expectedException \RuntimeException
	 */
	public function testGetStateThrowsRuntimeExceptionForMissingStateColumn()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('getColumnAlias'))
			->getMock();

		$tableMock->expects($this->once())
			->method('getColumnAlias')
			->willReturn('state');

		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('getTable'))
			->getMock();

		$entity->method('getTable')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999]);

		$entity->getState();
	}

	/**
	 * getState throws RuntimeException for missing state column.
	 *
	 * @return  void
	 */
	public function testGetStateReturnsCorrectValue()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('getColumnAlias'))
			->getMock();

		$tableMock
			->method('getColumnAlias')
			->willReturn('state');

		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('getTable'))
			->getMock();

		$entity->method('getTable')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => '0']);

		$this->assertSame(0, $entity->getState());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => 1]);

		$this->assertSame(1, $entity->getState(true));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => 0]);

		$this->assertSame(0, $entity->getState(true));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => null]);

		$this->assertSame(0, $entity->getState(true));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => '1']);

		$this->assertSame(1, $entity->getState(true));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => '']);

		$this->assertSame(0, $entity->getState(true));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => 'test']);

		$this->assertSame(0, $entity->getState(true));
	}

	/**
	 * isArchived returns correct value.
	 *
	 * @return  void
	 */
	public function testIsArchivedReturnsCorrectValue()
	{
		$entity = new EntityWithState;

		$reflection = new \ReflectionClass($entity);
		$stateProperty = $reflection->getProperty('state');
		$stateProperty->setAccessible(true);

		$stateProperty->setValue($entity, EntityWithState::STATE_UNPUBLISHED);

		$this->assertFalse($entity->isArchived());

		$stateProperty->setValue($entity, EntityWithState::STATE_ARCHIVED);

		$this->assertTrue($entity->isArchived());

		$stateProperty->setValue($entity, EntityWithState::STATE_PUBLISHED);

		$this->assertFalse($entity->isArchived());
	}

	/**
	 * isOnState returns correct value.
	 *
	 * @return  void
	 */
	public function testIsOnStateReturnsCorrectValue()
	{
		$entity = new EntityWithState;

		$reflection = new \ReflectionClass($entity);
		$stateProperty = $reflection->getProperty('state');
		$stateProperty->setAccessible(true);

		$stateProperty->setValue($entity, EntityWithState::STATE_ARCHIVED);
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_ARCHIVED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));

		$stateProperty->setValue($entity, EntityWithState::STATE_UNPUBLISHED);
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_PUBLISHED));

		$stateProperty->setValue($entity, EntityWithState::STATE_PUBLISHED);
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_PUBLISHED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));

		$stateProperty->setValue($entity, EntityWithState::STATE_TRASHED);
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_TRASHED));
	}

	/**
	 * isPublished returns correct value.
	 *
	 * @return  void
	 */
	public function testIsPublishedReturnsCorrectValue()
	{
		$entity = new EntityWithState;

		$reflection = new \ReflectionClass($entity);
		$stateProperty = $reflection->getProperty('state');
		$stateProperty->setAccessible(true);

		$stateProperty->setValue($entity, EntityWithState::STATE_UNPUBLISHED);

		$this->assertFalse($entity->isPublished());

		$stateProperty->setValue($entity, EntityWithState::STATE_PUBLISHED);

		$this->assertTrue($entity->isPublished());

		$stateProperty->setValue($entity, EntityWithState::STATE_ARCHIVED);

		$this->assertFalse($entity->isPublished());
	}

	/**
	 * isUnpublished returns correct value.
	 *
	 * @return  void
	 */
	public function testIsUnpublishedReturnsCorrectValue()
	{
		$entity = new EntityWithState;

		$reflection = new \ReflectionClass($entity);
		$stateProperty = $reflection->getProperty('state');
		$stateProperty->setAccessible(true);

		$stateProperty->setValue($entity, EntityWithState::STATE_PUBLISHED);

		$this->assertFalse($entity->isUnpublished());

		$stateProperty->setValue($entity, EntityWithState::STATE_UNPUBLISHED);

		$this->assertTrue($entity->isUnpublished());

		$stateProperty->setValue($entity, EntityWithState::STATE_ARCHIVED);

		$this->assertFalse($entity->isUnpublished());
	}

	/**
	 * isTrashed returns correct value.
	 *
	 * @return  void
	 */
	public function testIsTrashedReturnsCorrectValue()
	{
		$entity = new EntityWithState;

		$reflection = new \ReflectionClass($entity);
		$stateProperty = $reflection->getProperty('state');
		$stateProperty->setAccessible(true);

		$stateProperty->setValue($entity, EntityWithState::STATE_PUBLISHED);

		$this->assertFalse($entity->isTrashed());

		$stateProperty->setValue($entity, EntityWithState::STATE_TRASHED);

		$this->assertTrue($entity->isTrashed());

		$stateProperty->setValue($entity, EntityWithState::STATE_ARCHIVED);

		$this->assertFalse($entity->isTrashed());
	}
}
