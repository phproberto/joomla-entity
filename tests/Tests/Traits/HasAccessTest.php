<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\EntityWithAccess;

/**
 * HasAccess trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAccessTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * canAccess returns correct value.
	 *
	 * @return  void
	 */
	public function testCanAccessReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithAccess::class)
			->setMethods(array('checkAccess'))
			->getMock();

		$entity->expects($this->once())
			->method('checkAccess')
			->willReturn(false);

		$this->assertFalse($entity->canAccess());

		$entity = $this->getMockBuilder(EntityWithAccess::class)
			->setMethods(array('checkAccess'))
			->getMock();

		$entity->expects($this->once())
			->method('checkAccess')
			->willReturn(true);

		$this->assertTrue($entity->canAccess(true));
	}

	/**
	 * canAccess returns cached data.
	 *
	 * @return  void
	 */
	public function testCanAccessReturnsCachedData()
	{
		$entity = $this->getMockBuilder(EntityWithAccess::class)
			->setMethods(array('checkAccess'))
			->getMock();

		$entity->expects($this->once())
			->method('checkAccess')
			->willReturn(false);

		$reflection = new \ReflectionClass($entity);
		$accessProperty = $reflection->getProperty('access');
		$accessProperty->setAccessible(true);

		$this->assertFalse($entity->canAccess());
		$this->assertSame(false, $accessProperty->getValue($entity));

		$accessProperty->setValue($entity, true);

		$this->assertTrue($entity->canAccess());
	}

	/**
	 * getAccess returns correct data.
	 *
	 * @return  void
	 */
	public function testGetAccessReturnsCorrectValue()
	{
		$entity = new EntityWithAccess(999);

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'access' => 0]);

		$this->assertSame(0, $entity->getAccess());

		$rowProperty->setValue($entity, ['id' => 999, 'access' => 1]);

		$this->assertSame(1, $entity->getAccess());

		$rowProperty->setValue($entity, ['id' => 999, 'access' => 'nein']);

		$this->assertSame(0, $entity->getAccess());

		$rowProperty->setValue($entity, ['id' => 999, 'access' => '1']);

		$this->assertSame(1, $entity->getAccess());
	}

	/**
	 * getAccess uses correct column.
	 *
	 * @return  void
	 */
	public function testGetAccessUsesCorrectColumn()
	{
		$entity = $this->getMockBuilder(EntityWithAccess::class)
			->setMethods(array('getColumnAccess'))
			->getMock();

		$entity->method('getColumnAccess')
			->willReturn('access_level');

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'access_level' => 0]);

		$this->assertSame(0, $entity->getAccess());

		$rowProperty->setValue($entity, ['id' => 999, 'access_level' => 1]);

		$this->assertSame(1, $entity->getAccess());

		$rowProperty->setValue($entity, ['id' => 999, 'access_level' => 'nein']);

		$this->assertSame(0, $entity->getAccess());

		$rowProperty->setValue($entity, ['id' => 999, 'access_level' => '1']);

		$this->assertSame(1, $entity->getAccess());
	}
}
