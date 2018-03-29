<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithFeatured;

/**
 * HasFeatured trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasFeaturedTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithFeatured::clearAll();

		parent::tearDown();
	}

	/**
	 * isFeatured returns correct value.
	 *
	 * @return  void
	 */
	public function testIsFeaturedReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, Column::FEATURED => 0));

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$this->assertFalse($entity->isFeatured(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::FEATURED => '0'));

		$this->assertFalse($entity->isFeatured(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::FEATURED => '1'));

		$this->assertTrue($entity->isFeatured(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::FEATURED => 1));

		$this->assertTrue($entity->isFeatured(true));
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
		$entity = $this->getMockBuilder(EntityWithFeatured::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('featured');

		$entity->bind($row);

		return $entity;
	}
}
