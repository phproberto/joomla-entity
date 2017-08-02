<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Client\Administrator;
use Phproberto\Joomla\Client\Site;
use Phproberto\Joomla\Entity\Core\Extension\Component;
use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\ClassWithComponent;

/**
 * HasComponent trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasComponentTest extends \TestCaseDatabase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		\JFactory::$session     = $this->getMockSession();
		\JFactory::$config      = $this->getMockConfig();
		\JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ClassWithComponent::clearAllInstances();

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');

		return $dataSet;
	}

	/**
	 * component calls loadComponent.
	 *
	 * @return  void
	 */
	public function testComponentClassLoadComponent()
	{
		$dummyComponent = new Component(999);

		$class = $this->getMockBuilder(ClassWithComponent::class)
			->setMethods(array('loadComponent'))
			->getMock();

		$class->expects($this->once())
			->method('loadComponent')
			->willReturn($dummyComponent);

		$this->assertSame($dummyComponent, $class->component());
	}

	/**
	 * component returns cached component.
	 *
	 * @return  void
	 */
	public function testComponentReturnsCachedComponent()
	{
		$class = $this->getMockBuilder(ClassWithComponent::class)
			->setMethods(array('loadComponent'))
			->getMock();

		$class->expects($this->never())
			->method('loadComponent');

		$reflection = new \ReflectionClass($class);

		$rowProperty = $reflection->getProperty('component');
		$rowProperty->setAccessible(true);

		$dummyComponent = new Component(999);

		$rowProperty->setValue($class, $dummyComponent);

		$this->assertSame($dummyComponent, $class->component());
	}

	/**
	 * componentOption returns correct value.
	 *
	 * @return  void
	 */
	public function testComponentOptionFromClass()
	{
		$class = new ClassWithComponent;

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('componentOption');
		$method->setAccessible(true);

		$this->assertSame('com_tests', $method->invoke($class));

		require_once __DIR__ . '/Stubs/ContentEntityComponent.php';

		$class = new \ContentEntityComponent;

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('componentOption');
		$method->setAccessible(true);

		$this->assertSame('com_content', $method->invoke($class));
	}

	/**
	 * loadComponent returns correct value.
	 *
	 * @return  void
	 */
	public function testLoadComponentReturnsCorrectValue()
	{
		$class = $this->getMockBuilder(ClassWithComponent::class)
			->setMethods(array('componentOption'))
			->getMock();

		$class->expects($this->once())
			->method('componentOption')
			->willReturn('com_content');

		$reflection = new \ReflectionClass($class);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$method = $reflection->getMethod('loadComponent');
		$method->setAccessible(true);

		$this->assertInstanceOf(Component::class, $method->invoke($class));
	}
}
