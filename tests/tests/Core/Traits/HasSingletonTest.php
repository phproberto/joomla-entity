<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\ClassWithSingleton;

/**
 * HasSingleton tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasSingletonTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsInstanceOfSameClass()
	{
		$class = new ClassWithSingleton;

		$this->assertInstanceOf(ClassWithSingleton::class, $class->instance());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsCachedInstance()
	{
		$class = new ClassWithSingleton;

		$reflection = new \ReflectionClass(ClassWithSingleton::class);
		$instanceProperty = $reflection->getProperty('instance');
		$instanceProperty->setAccessible(true);

		$instanceProperty->setValue(ClassWithSingleton::class, $class);

		$this->assertSame($class, ClassWithSingleton::instance());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function clearInstanceClearsCachedSingleton()
	{
		$class = new ClassWithSingleton;

		$reflection = new \ReflectionClass(ClassWithSingleton::class);
		$instanceProperty = $reflection->getProperty('instance');
		$instanceProperty->setAccessible(true);

		$instanceProperty->setValue(ClassWithSingleton::class, $class);

		$this->assertSame($class, ClassWithSingleton::instance());

		ClassWithSingleton::clearInstance();

		$this->assertNotSame($class, ClassWithSingleton::instance());
	}
}
