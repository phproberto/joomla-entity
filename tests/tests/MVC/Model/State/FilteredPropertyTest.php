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
use Phproberto\Joomla\Entity\MVC\Model\State\FilteredProperty;
use Phproberto\Joomla\Entity\MVC\Model\State\PropertyInterface;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\StringQuoted;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\FilterInterface;

/**
 * FilteredProperty tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class FilteredPropertyTest extends TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsPropertyAndFilter()
	{
		$property = $this->propertyMock('filter.sample');
		$filter = $this->getMock(FilterInterface::class);

		$filteredProperty = new FilteredProperty($property, $filter);

		$reflection = new \ReflectionClass($filteredProperty);
		$propertyProperty = $reflection->getProperty('property');
		$propertyProperty->setAccessible(true);
		$filterProperty = $reflection->getProperty('filter');
		$filterProperty->setAccessible(true);

		$this->assertSame($property, $propertyProperty->getValue($filteredProperty));
		$this->assertSame($filter, $filterProperty->getValue($filteredProperty));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function filterCallsFilterFilterMethod()
	{
		$property = $this->propertyMock('filter.sample');

		$filter = $this->getMockBuilder(FilterInterface::class)
			->setMethods(array('filter'))
			->getMock();

		$filter->expects($this->once())
			->method('filter')
			->with($this->equalTo('myValue'))
			->willReturn(23);

		$filteredProperty = new FilteredProperty($property, $filter);

		$this->assertSame(23, $filteredProperty->filter('myValue'));
	}


	/**
	 * @test
	 *
	 * @return void
	 */
	public function noFilterLoadsStringQuotedFilter()
	{
		$property = $this->propertyMock('filter.sample');

		$filteredProperty = new FilteredProperty($property);

		$reflection = new \ReflectionClass($filteredProperty);
		$filterProperty = $reflection->getProperty('filter');
		$filterProperty->setAccessible(true);

		$this->assertInstanceOf(StringQuoted::class, $filterProperty->getValue($filteredProperty));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function undefinedMethodCallsAreRedirectedToProperty()
	{
		$property = $this->getMockBuilder(PropertyInterface::class)
			->setMethods(array('key', 'isPopulable', 'testMethod'))
			->getMock();

		$property->expects($this->once())
			->method('testMethod')
			->with($this->equalTo('param1'), $this->equalTo('param2'))
			->willReturn(23);

		$filteredProperty = new FilteredProperty($property);

		$this->assertSame(23, $filteredProperty->testMethod('param1', 'param2'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPopulableReturnsPropertyIsPopulable()
	{
		$property = $this->propertyMock('filter.sample');

		$filteredProperty = new FilteredProperty($property);

		$this->assertFalse($filteredProperty->isPopulable());

		$property = $this->propertyMock('filter.sample', true);

		$filteredProperty = new FilteredProperty($property);

		$this->assertTrue($filteredProperty->isPopulable());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function keyReturnsPropertyKey()
	{
		$property = $this->propertyMock('filter.sample');

		$filteredProperty = new FilteredProperty($property);

		$this->assertSame('filter.sample', $filteredProperty->key());

		$property = $this->propertyMock('list.test', true);

		$filteredProperty = new FilteredProperty($property);

		$this->assertSame('list.test', $filteredProperty->key());
	}

	/**
	 * Generate a property mock object for testing purposes.
	 *
	 * @param   string   $key          Property key
	 * @param   boolean  $isPopulable  Use populable property?
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
