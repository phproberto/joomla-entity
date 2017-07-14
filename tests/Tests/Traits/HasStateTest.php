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
	 * columnState returns table value.
	 *
	 * @return  void
	 */
	public function testColumnStateReturnsTableValue()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('getColumnAlias'))
			->getMock();

		$tableMock->expects($this->once())
			->method('getColumnAlias')
			->willReturn('state');

		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('table'))
			->getMock();

		$entity->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('columnState');
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
			->setMethods(array('table'))
			->getMock();

		$entity->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999]);

		$entity->state();
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
			->setMethods(array('table'))
			->getMock();

		$entity->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => '0']);

		$this->assertSame(0, $entity->state());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => 1]);

		$this->assertSame(1, $entity->state());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => 0]);

		$this->assertSame(0, $entity->state());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => null]);

		$this->assertSame(0, $entity->state());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => '1']);

		$this->assertSame(1, $entity->state());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => '']);

		$this->assertSame(0, $entity->state());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => 'test']);

		$this->assertSame(0, $entity->state());
	}

	/**
	 * isArchived returns correct value.
	 *
	 * @return  void
	 */
	public function testIsArchivedReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_UNPUBLISHED]);

		$this->assertFalse($entity->isArchived());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_ARCHIVED]);

		$this->assertTrue($entity->isArchived());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);

		$this->assertFalse($entity->isArchived());
	}

	/**
	 * isDisabled returns correct value.
	 *
	 * @return  void
	 */
	public function testIsDisabledReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_UNPUBLISHED]);

		$this->assertTrue($entity->isDisabled());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);

		$this->assertFalse($entity->isDisabled());
	}

	/**
	 * isEnabled returns correct value.
	 *
	 * @return  void
	 */
	public function testIsEnabledReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_UNPUBLISHED]);

		$this->assertFalse($entity->isEnabled());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);

		$this->assertTrue($entity->isEnabled());
	}

	/**
	 * isOnState returns correct value.
	 *
	 * @return  void
	 */
	public function testIsOnStateReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_ARCHIVED]);
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_ARCHIVED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_UNPUBLISHED]);
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_PUBLISHED));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_PUBLISHED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_TRASHED]);
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
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_UNPUBLISHED]);

		$this->assertFalse($entity->isPublished());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);

		$this->assertTrue($entity->isPublished());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_ARCHIVED]);

		$this->assertFalse($entity->isPublished());
	}

	/**
	 * isUnpublished returns correct value.
	 *
	 * @return  void
	 */
	public function testIsUnpublishedReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);

		$this->assertFalse($entity->isUnpublished());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_UNPUBLISHED]);

		$this->assertTrue($entity->isUnpublished());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_ARCHIVED]);

		$this->assertFalse($entity->isUnpublished());
	}

	/**
	 * isTrashed returns correct value.
	 *
	 * @return  void
	 */
	public function testIsTrashedReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnState'))
			->getMock();

		$entity->method('columnState')
			->willReturn('state');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_PUBLISHED]);

		$this->assertFalse($entity->isTrashed());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_TRASHED]);

		$this->assertTrue($entity->isTrashed());

		$rowProperty->setValue($entity, ['id' => 999, 'state' => EntityWithState::STATE_ARCHIVED]);

		$this->assertFalse($entity->isTrashed());
	}

	/**
	 * availableStates returns available states.
	 *
	 * @return  void
	 */
	public function testAvailableStatesReturnsAvailableStates()
	{
		$entity = new EntityWithState;

		$this->assertSame(4, count($entity->availableStates()));

		$customStates = array(
			56   => 'Dead or alive',
			99   => 'Broken by Dimitris',
			999  => 'Sold to Google by Ronni',
			1001 => 'Drinking beer'
		);

		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('availableStates'))
			->getMock();

		$entity->expects($this->once())
			->method('availableStates')
			->willReturn($customStates);

		$this->assertSame($customStates, $entity->availableStates());
	}
}
