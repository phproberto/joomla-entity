<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Mock;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Extensions\Entity\Component;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Component mock generator.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ComponentMock
{
	/**
	 * Retrieve an instance
	 *
	 * @param   \PHPUnit_Framework_TestCase  $testCase  Test class that will use the mock
	 * @param   array                        $methods   Methods to customise
	 *
	 * @return  Component
	 */
	public static function instance(\PHPUnit_Framework_TestCase $testCase, array $methods = [])
	{
		$methods = array_unique(array_merge($methods, ['id', 'option']));

		return $testCase->getMockBuilder(Component::class)
			->disableOriginalConstructor()
			->setMethods($methods)
			->getMock();
	}

	/**
	 * Inject a component into an entity.
	 *
	 * @param   Component        $componentMock  Component to inject
	 * @param   EntityInterface  $entity         Entity to inject the mock
	 *
	 * @return  void
	 */
	public static function injectToEntity(Component $componentMock, EntityInterface $entity)
	{
		$reflection = new \ReflectionClass($entity);
		$componentProperty = $reflection->getProperty('component');
		$componentProperty->setAccessible(true);
		$componentProperty->setValue($entity, $componentMock);
	}

	/**
	 * Save to cache.
	 *
	 * @param   Component  $component  Component to save to cache.
	 *
	 * @return  void
	 */
	public static function saveToCache(Component $component)
	{
		$reflection = new \ReflectionClass(Component::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$cachedInstances = [
			Component::class => [
				$component->id() => $component
			]
		];

		$instancesProperty->setValue(Component::class, $cachedInstances);
	}

	/**
	 * Save component option<->id relationship in cache.
	 *
	 * @param   Component  $component  Component to save
	 *
	 * @return  void
	 */
	public static function saveOptionIdXref(Component $component)
	{
		$reflection = new \ReflectionClass(Component::class);
		$optionIdXrefProperty = $reflection->getProperty('optionIdXref');
		$optionIdXrefProperty->setAccessible(true);

		$optionIdXrefProperty->setValue(Component::class, [$component->option() => $component->id()]);
	}
}
