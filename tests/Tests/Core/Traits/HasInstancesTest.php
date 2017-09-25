<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\AnotherClassWithInstances;
use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\ClassWithInstances;

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
	 * clearAllInstances clears all the instances.
	 *
	 * @return  void
	 */
	public function testClearAllInstancesClearsAllTheInstances()
	{
		$reflection = new \ReflectionClass(ClassWithInstances::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = [
			ClassWithInstances::class => [
				1337 => new ClassWithInstances(1337),
				1338 => new ClassWithInstances(1338)
			]
		];

		$instancesProperty->setValue(ClassWithInstances::class, $instances);

		$this->assertEquals($instances, $instancesProperty->getValue(ClassWithInstances::class));

		ClassWithInstances::clearAllInstances();

		$this->assertEquals([], $instancesProperty->getValue(ClassWithInstances::class));
	}

	/**
	 * Test clearInstance method.
	 *
	 * @return  void
	 */
	public function testClearInstance()
	{
		$class = ClassWithInstances::instance(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = ClassWithInstances::instance(1337);
		$this->assertEquals('Sample name', $class2->getName());

		ClassWithInstances::clearInstance(1337);

		$class3 = ClassWithInstances::instance(1337);
		$this->assertNotEquals('Sample name', $class3->getName());
	}

	/**
	 * Test getFreshInstance method.
	 *
	 * @return  void
	 */
	public function testFreshInstance()
	{
		$class = ClassWithInstances::instance(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class3 = ClassWithInstances::freshInstance(1337);
		$this->assertNotEquals('Sample name', $class3->getName());
	}

	/**
	 * Test getInstance method.
	 *
	 * @return  void
	 */
	public function testGetInstance()
	{
		$class = ClassWithInstances::instance(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = ClassWithInstances::instance(1337);
		$this->assertEquals('Sample name', $class2->getName());
	}

	/**
	 * Test that different classes using the same id.
	 *
	 * @return  void
	 */
	public function testNoCollisionsBetweenClasses()
	{
		$class = ClassWithInstances::instance(1337);
		$class->setName('Sample name');
		$this->assertEquals('Sample name', $class->getName());

		$class2 = AnotherClassWithInstances::instance(1337);
		$this->assertNotEquals('Sample name', $class2->getName());

		$class3 = ClassWithInstances::instance(1337);
		$this->assertEquals('Sample name', $class3->getName());
	}
}
