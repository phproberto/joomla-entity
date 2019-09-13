<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State;

defined('_JEXEC') || die;

use PHPUnit\Framework\TestCase;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Phproberto\Joomla\Entity\MVC\Model\State\State;
use Phproberto\Joomla\Entity\MVC\Model\State\StateInterface;
use Phproberto\Joomla\Entity\MVC\Model\State\PropertyInterface;

/**
 * State tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class StateTest extends TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function implementsStateInterface()
	{
		$state = new State($this->getMock(BaseDatabaseModel::class));

		$this->assertInstanceOf(StateInterface::class, $state);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsModelAndProperties()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$properties = [
			$this->propertyMock('filter.sample'),
			$this->propertyMock('list.sample')
		];

		$state = new State($model, $properties);

		$reflection = new \ReflectionClass($state);
		$modelProperty = $reflection->getProperty('model');
		$modelProperty->setAccessible(true);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$this->assertSame($model, $modelProperty->getValue($state));
		$this->assertSame(2, count($propertiesProperty->getValue($state)));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function addPropertyAddsAProperty()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$this->assertEquals([], $propertiesProperty->getValue($state));

		$property = $this->propertyMock('my.property');

		$state->addProperty($property);

		$this->assertEquals(['my.property' => $property], $propertiesProperty->getValue($state));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function addPropertyOverwritesPropertyWithKey()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$property = $this->propertyMock('filter.sample');

		$properties = [	'filter.sample' => $property ];

		$propertiesProperty->setValue($state, $properties);

		$property2 = clone $property;

		$state->addProperty($property2);

		$this->assertNotSame(['filter.sample' => $property], $propertiesProperty->getValue($state));
		$this->assertSame(['filter.sample' => $property2], $propertiesProperty->getValue($state));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function addPropertiesAddsProperties()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$this->assertEquals([], $propertiesProperty->getValue($state));

		$properties = [
			'filter.sample' => $this->propertyMock('filter.sample'),
			'list.sample'   => $this->propertyMock('list.sample')
		];

		$state->addProperties($properties);

		$this->assertEquals($properties, $propertiesProperty->getValue($state));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getCallsModelGetMethod()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)
			->setMethods(array('getState'))
			->getMock();

		$model->expects($this->once())
			->method('getState')
			->with($this->equalTo('filter.sample'), $this->equalTo('defaultValue'))
			->willReturn(23);

		$state = new State($model);

		$this->assertSame(23, $state->get('filter.sample', 'defaultValue'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasPropertyReturnsCorrectValue()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$this->assertFalse($state->hasProperty('filter.sample'));

		$properties = [
			'filter.sample' => $this->propertyMock('filter.sample')
		];

		$propertiesProperty->setValue($state, $properties);

		$this->assertTrue($state->hasProperty('filter.sample'));
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException \RuntimeException
	 */
	public function propertyThrowsExceptionWhenPropertyDoesNotExist()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$state->property('filter.sample');
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function propertyReturnsExistingProperty()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$property = $this->propertyMock('filter.sample');

		$properties = [
			'filter.sample' => $property
		];

		$propertiesProperty->setValue($state, $properties);

		$this->assertSame($property, $state->property('filter.sample'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function propertiesReturnsProperties()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$property = $this->propertyMock('filter.sample');

		$properties = [
			'filter.sample' => $property
		];

		$this->assertEquals([], $state->properties());

		$propertiesProperty->setValue($state, $properties);

		$this->assertSame($properties, $state->properties());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function populablePropertiesReturnsCorrectValue()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)->getMock();

		$state = new State($model);

		$reflection = new \ReflectionClass($state);
		$propertiesProperty = $reflection->getProperty('properties');
		$propertiesProperty->setAccessible(true);

		$property = $this->propertyMock('filter.unpopulable');
		$property2 = $this->propertyMock('filter.populable', true);

		$properties = [
			'filter.unpopulable' => $property,
			'filter.populable' => $property2
		];

		$this->assertEquals([], $state->populableProperties());

		$propertiesProperty->setValue($state, $properties);

		$this->assertEquals(['filter.populable' => $property2], $state->populableProperties());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function setCallsModelSetStateMethod()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)
			->setMethods(array('setState', 'getState'))
			->getMock();

		$model->expects($this->once())
			->method('setState')
			->with($this->equalTo('filter.sample'), $this->equalTo('myValue'))
			->willReturn(null);

		$state = new State($model);

		$state->set('filter.sample', 'myValue');
	}

	/**
	 * Generate a property mock object for testing purposes.
	 *
	 * @param   string   $key          Property key
	 * @param   boolean  $isPopulable  Is this property populable?
	 *
	 * @return  PropertyInterface
	 */
	private function propertyMock($key, $isPopulable = false)
	{
		$property = $this->getMockBuilder(PropertyInterface::class)
			->setMethods(array('key', 'isPopulable'))
			->getMock();

		$property->method('key')->willReturn($key);
		$property->method('isPopulable')->willReturn($isPopulable);

		return $property;
	}
}
