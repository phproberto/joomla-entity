<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;

/**
 * HasEvents trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasEventsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getDispatcher returns correct class.
	 *
	 * @return  void
	 */
	public function testGetDispatcherReturnsCorrectClass()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('dispatcher');
		$method->setAccessible(true);

		$this->assertInstanceOf(\JEventDispatcher::class, $method->invoke($entity));
	}
	/**
	 * importPlugin imports plugin.
	 *
	 * @return  void
	 */
	public function testImportPluginImportsPlugin()
	{
		$entity = $this->getMockedEntity();

		$reflection = new \ReflectionClass($entity);

		$eventsPluginsImportedProperty = $reflection->getProperty('eventsPluginsImported');
		$eventsPluginsImportedProperty->setAccessible(true);

		$this->assertSame(array(), $eventsPluginsImportedProperty->getValue($entity));

		$entity->importPlugin('my_folder');

		$this->assertSame(array('my_folder'), $eventsPluginsImportedProperty->getValue($entity));
	}

	/**
	 * trigger runs trigger method on dispatcher.
	 *
	 * @return  void
	 */
	public function testTriggerExecutesTriggerMethodOnDispatcher()
	{
		$response = array('result 1', 'result 2');

		$entity = $this->getMockedEntity(1, $response);

		$this->assertSame($response, $entity->trigger('event_name', array('param1', 'param2')));
	}

	/**
	 * trigger imports default plugins.
	 *
	 * @return  void
	 */
	public function testTriggerImportsDefaultPlugins()
	{
		$entity = $this->getMockedEntity();

		$reflection = new \ReflectionClass($entity);

		$eventsPluginsImportedProperty = $reflection->getProperty('eventsPluginsImported');
		$eventsPluginsImportedProperty->setAccessible(true);

		$entity->trigger('sample_event');

		$this->assertSame(array('joomla_entity'), $eventsPluginsImportedProperty->getValue($entity));
	}

	/**
	 * Get a mocked entity with bypassed importJoomlaPlugin method to avoid testing issues.
	 *
	 * @param   integer  $id        Identifier to assign
	 * @param   array    $response  Expected response from the dispatcher
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getMockedEntity($id = null, $response  = array())
	{
		$dispatcher = $this->getMockBuilder(\JEventDispatcher::class)
			->setMethods(array('trigger'))
			->getMock();

		$dispatcher->method('trigger')
			->willReturn($response);

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('importJoomlaPlugin', 'dispatcher'))
			->getMock();

		$entity->method('importJoomlaPlugin')
			->willReturn(true);

		$entity->method('dispatcher')
			->willReturn($dispatcher);

		if ($id)
		{
			$reflection = new \ReflectionClass($entity);
			$idProperty = $reflection->getProperty('id');
			$idProperty->setAccessible(true);
			$idProperty->setValue($entity, $id);
		}

		return $entity;
	}
}
