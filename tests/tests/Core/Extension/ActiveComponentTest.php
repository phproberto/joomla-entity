<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Extension;

use Phproberto\Joomla\Entity\Core\Extension\ActiveComponent;
use Phproberto\Joomla\Entity\Exception\InvalidEntityData;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;

/**
 * Component entity tests.
 *
 * @since   1.1.0
 */
class ActiveComponentTest extends \TestCaseDatabase
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
		ActiveComponent::clearAll();

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
	 * fetchRow returns correct value.
	 *
	 * @return  void
	 */
	public function testFetchRowReturnsCorrectValue()
	{
		$class = $this->getMockBuilder(ActiveComponent::class)
			->setMethods(array('option'))
			->getMock();

		$class->expects($this->once())
			->method('option')
			->willReturn('com_content');

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$this->assertSame(22, (int) $method->invoke($class)['extension_id']);
	}

	/**
	 * fetchRow throws an exception when no active option is detected.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testFetchRowThrowsAnExceptionWhenNoActiveOptionIsDetected()
	{
		$class = $this->getMockBuilder(ActiveComponent::class)
			->setMethods(array('option'))
			->getMock();

		$class->expects($this->once())
			->method('option')
			->willReturn(null);

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($class);
	}

	/**
	 * fetchRow throws an exception when component not found.
	 *
	 * @return  void
	 *
	 * @expectedException Phproberto\Joomla\Entity\Exception\InvalidEntityData
	 */
	public function testFetchRowThrowsAnExceptionWhenComponentNotFound()
	{
		$table = $this->getMockBuilder('MockTable')
			->setMethods(array('load', 'getProperties'))
			->getMock();

		$table->expects($this->once())
			->method('load')
			->willReturn(true);

		$table->expects($this->once())
			->method('getProperties')
			->willReturn(array());

		$class = $this->getMockBuilder(ActiveComponent::class)
			->setMethods(array('option', 'table'))
			->getMock();

		$class->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$class->expects($this->once())
			->method('table')
			->willReturn($table);

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($class);
	}

	/**
	 * fetchRow throws an exception when component not found.
	 *
	 * @return  void
	 *
	 * @expectedException Phproberto\Joomla\Entity\Exception\LoadEntityDataError
	 */
	public function testFetchRowThrowsAnExceptionWhenNoPrimaryKeyReturned()
	{
		$class = $this->getMockBuilder(ActiveComponent::class)
			->setMethods(array('option'))
			->getMock();

		$class->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($class);
	}

	/**
	 * option returns active input option.
	 *
	 * @return  void
	 */
	public function testOptionReturnsActiveInputOption()
	{
		$component = new ActiveComponent;

		\JFactory::getApplication()->input->set('option', 'com_phproberto');

		$this->assertSame('com_phproberto', $component->option());

		\JFactory::getApplication()->input->set('option', null);
	}
}
