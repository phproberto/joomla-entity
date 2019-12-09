<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\ClassWithStaticCache;

/**
 * HasAssociatedEntity trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasStaticCacheTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function clearStaticCacheClearsCache()
	{
		$reflection = new \ReflectionClass(ClassWithStaticCache::class);
		$staticCacheProperty = $reflection->getProperty('staticCache');
		$staticCacheProperty->setAccessible(true);

		$cache = [
			ClassWithStaticCache::class => [
				'mycache' => 'myvalue'
			]
		];

		$this->assertEquals([], $staticCacheProperty->getValue(ClassWithStaticCache::class));

		$staticCacheProperty->setValue(ClassWithStaticCache::class, $cache);
		$this->assertEquals($cache, $staticCacheProperty->getValue(ClassWithStaticCache::class));

		ClassWithStaticCache::clearStaticCache();

		$this->assertEquals([ClassWithStaticCache::class => []], $staticCacheProperty->getValue(ClassWithStaticCache::class));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getFromStaticCacheReturnsCachedValue()
	{
		$class = new ClassWithStaticCache;

		$reflection = new \ReflectionClass($class);
		$staticCacheProperty = $reflection->getProperty('staticCache');
		$staticCacheProperty->setAccessible(true);

		$cache = [
			ClassWithStaticCache::class => [
				'my-key' => 'my-value'
			]
		];

		$staticCacheProperty->setValue($class, $cache);

		$method = $reflection->getMethod('getFromStaticCache');
		$method->setAccessible(true);

		$this->assertEquals('my-value', $method->invoke($class, 'my-key'));
	}
}
