<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithFeatured;

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
		EntityWithFeatured::clearAllInstances();

		parent::tearDown();
	}

	/**
	 * isFeatured returns cached value.
	 *
	 * @return  void
	 */
	public function testIsFeaturedReturnsCachedValue()
	{
		$entity = new EntityWithFeatured(999);

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999));

		$featuredProperty = $reflection->getProperty('featured');
		$featuredProperty->setAccessible(true);

		$this->assertFalse($entity->isFeatured());
		$this->assertSame(false, $featuredProperty->getValue($entity));

		$featuredProperty->setValue($entity, true);

		$this->assertTrue($entity->isFeatured());
	}

	/**
	 * isFeatured reloads data.
	 *
	 * @return  void
	 */
	public function testIsFeaturedReloadsData()
	{
		$entity = new EntityWithFeatured(999);

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999));
		$this->assertFalse($entity->isFeatured());

		$rowProperty->setValue($entity, array('id' => 999, 'featured' => 1));
		$this->assertFalse($entity->isFeatured());
		$this->assertTrue($entity->isFeatured(true));
	}
	/**
	 * isFeatured returns correct value.
	 *
	 * @return  void
	 */
	public function testIsFeaturedReturnsCorrectValue()
	{
		$entity = new EntityWithFeatured(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999));

		$this->assertFalse($entity->isFeatured());

		$rowProperty->setValue($entity, array('id' => 999, 'featured' => 0));

		$this->assertFalse($entity->isFeatured(true));

		$rowProperty->setValue($entity, array('id' => 999, 'featured' => '0'));

		$this->assertFalse($entity->isFeatured(true));

		$rowProperty->setValue($entity, array('id' => 999, 'featured' => '1'));

		$this->assertTrue($entity->isFeatured(true));

		$rowProperty->setValue($entity, array('id' => 999, 'featured' => 1));

		$this->assertTrue($entity->isFeatured(true));
	}
}
