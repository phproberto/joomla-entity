<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\AnotherClassWithInstances;
use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\ClassWithInstances;

/**
 * Tests for HasInstances trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class HasInstancesTest extends \TestCase
{
	/**
	 * Test constructor.
	 *
	 * @return  void
	 */
	public function testConstructor()
	{
		$class = new ClassWithInstances(1337);

		$this->assertEquals(1337, $class->getId());
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = new ClassWithInstances(1337);
		$this->assertNotEquals('Sample name', $class2->getName());
	}

	/**
	 * clearAll clears all the instances.
	 *
	 * @return  void
	 */
	public function testClearAllClearsAllTheInstances()
	{
		$reflection = new \ReflectionClass(ClassWithInstances::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = array(
			ClassWithInstances::class => array(
				1337 => new ClassWithInstances(1337),
				1338 => new ClassWithInstances(1338)
			)
		);

		$instancesProperty->setValue(ClassWithInstances::class, $instances);

		$this->assertEquals($instances, $instancesProperty->getValue(ClassWithInstances::class));

		ClassWithInstances::clearAll();

		$this->assertEquals(array(), $instancesProperty->getValue(ClassWithInstances::class));
	}

	/**
	 * Test clear method.
	 *
	 * @return  void
	 */
	public function testClear()
	{
		$class = ClassWithInstances::find(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = ClassWithInstances::find(1337);
		$this->assertEquals('Sample name', $class2->getName());

		ClassWithInstances::clear(1337);

		$class3 = ClassWithInstances::find(1337);
		$this->assertNotEquals('Sample name', $class3->getName());
	}

	/**
	 * Test getFresh method.
	 *
	 * @return  void
	 */
	public function testFresh()
	{
		$class = ClassWithInstances::find(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class3 = ClassWithInstances::fresh(1337);
		$this->assertNotEquals('Sample name', $class3->getName());
	}

	/**
	 * Test find method.
	 *
	 * @return  void
	 */
	public function testFind()
	{
		$class = ClassWithInstances::find(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = ClassWithInstances::find(1337);
		$this->assertEquals('Sample name', $class2->getName());
	}

	/**
	 * Test that different classes using the same id.
	 *
	 * @return  void
	 */
	public function testNoCollisionsBetweenClasses()
	{
		$class = ClassWithInstances::find(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = AnotherClassWithInstances::find(1337);
		$this->assertNotEquals('Sample name', $class2->getName());

		$class3 = ClassWithInstances::find(1337);
		$this->assertEquals('Sample name', $class3->getName());
	}
}
