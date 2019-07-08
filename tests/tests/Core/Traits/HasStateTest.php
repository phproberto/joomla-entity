<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithState;

/**
 * HasState trait tests.
 *
 * @since   1.1.0
 */
class HasStateTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Column to use to load/store state.
	 *
	 * @const
	 */
	const COLUMN_STATE = 'published';

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithState::clearAll();

		parent::tearDown();
	}

	/**
	 * getState throws RuntimeException for missing state column.
	 *
	 * @return  void
	 *
	 @expectedException \InvalidArgumentException
	 */
	public function testGetStateThrowsRuntimeExceptionForMissingStateColumn()
	{
		$entity = $this->getEntity(array('id' => 999));

		$entity->state();
	}

	/**
	 * state returns correct value.
	 *
	 * @return  void
	 */
	public function testStateReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => '0'));

		$this->assertSame(0, $entity->state());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => 1));

		$this->assertSame(1, $entity->state());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => 0));

		$this->assertSame(0, $entity->state());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => null));

		$this->assertSame(0, $entity->state());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => '1'));

		$this->assertSame(1, $entity->state());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => ''));

		$this->assertSame(0, $entity->state());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => 'test'));

		$this->assertSame(0, $entity->state());
	}

	/**
	 * isArchived returns correct value.
	 *
	 * @return  void
	 */
	public function testIsArchivedReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_UNPUBLISHED));

		$this->assertFalse($entity->isArchived());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_ARCHIVED));

		$this->assertTrue($entity->isArchived());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));

		$this->assertFalse($entity->isArchived());
	}

	/**
	 * isDisabled returns correct value.
	 *
	 * @return  void
	 */
	public function testIsDisabledReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_UNPUBLISHED));

		$this->assertTrue($entity->isDisabled());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));

		$this->assertFalse($entity->isDisabled());
	}

	/**
	 * isEnabled returns correct value.
	 *
	 * @return  void
	 */
	public function testIsEnabledReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_UNPUBLISHED));

		$this->assertFalse($entity->isEnabled());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));

		$this->assertTrue($entity->isEnabled());
	}

	/**
	 * isOnState returns correct value.
	 *
	 * @return  void
	 */
	public function testIsOnStateReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_ARCHIVED));

		$this->assertTrue($entity->isOnState(EntityWithState::STATE_ARCHIVED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_UNPUBLISHED));
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_PUBLISHED));

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));
		$this->assertTrue($entity->isOnState(EntityWithState::STATE_PUBLISHED));
		$this->assertFalse($entity->isOnState(EntityWithState::STATE_UNPUBLISHED));

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_TRASHED));
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
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_UNPUBLISHED));

		$this->assertFalse($entity->isPublished());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));

		$this->assertTrue($entity->isPublished());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_ARCHIVED));

		$this->assertFalse($entity->isPublished());
	}

	/**
	 * isUnpublished returns correct value.
	 *
	 * @return  void
	 */
	public function testIsUnpublishedReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));

		$this->assertFalse($entity->isUnpublished());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_UNPUBLISHED));

		$this->assertTrue($entity->isUnpublished());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_ARCHIVED));

		$this->assertFalse($entity->isUnpublished());
	}

	/**
	 * isTrashed returns correct value.
	 *
	 * @return  void
	 */
	public function testIsTrashedReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_PUBLISHED));

		$this->assertFalse($entity->isTrashed());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_TRASHED));

		$this->assertTrue($entity->isTrashed());

		$entity = $this->getEntity(array('id' => 999, static::COLUMN_STATE => EntityWithState::STATE_ARCHIVED));

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

	/**
	 * Get a mocked entity.
	 *
	 * @param   array  $row  Row returned by the entity as data
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getEntity($row = array())
	{
		$entity = $this->getMockBuilder(EntityWithState::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::COLUMN_STATE);

		$entity->bind($row);

		return $entity;
	}
}
