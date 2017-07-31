<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Tests\Stubs\Entity;

/**
 * Base entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityTest extends \TestCase
{
	/**
	 * Name of the primary key
	 *
	 * @const
	 */
	const PRIMARY_KEY = 'id';

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
		$this->restoreFactoryState();

		Entity::clearAllInstances();

		parent::tearDown();
	}

	/**
	 * Get an entity that simulates data loading.
	 *
	 * @param   array   $data  Expected data loaded
	 *
	 * @return  PHPUnit_Framework_MockObject_MockObject
	 */
	private function getLoadableEntityMock(array $data = array())
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('load', 'getProperties'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('load')
			->willReturn(true);

		$tableMock->expects($this->at(1))
			->method('getProperties')
			->willReturn($data);

		$mock = $this->getMockBuilder(Entity::class)
			->setMethods(array('table'))
			->getMock();

		$mock
			->method('table')
			->willReturn($tableMock);

		return $mock;
	}

	/**
	 * Assign sets the correct value.
	 *
	 * @return  void
	 */
	public function testAsssignSetsCorrectValue()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$this->assertSame(null, $rowProperty->getValue($entity));

		$entity->assign('name', 'Sample name');

		$this->assertSame(array('name' => 'Sample name'), $rowProperty->getValue($entity));
	}

	/**
	 * Assign sets correct id.
	 *
	 * @return  void
	 */
	public function testAssignSetsCorrectId()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(0, $idProperty->getValue($entity));

		$entity->assign(self::PRIMARY_KEY, '999');

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * bind sets correct object data.
	 *
	 * @return  void
	 */
	public function testBindSetsCorrectData()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$this->assertSame(null, $rowProperty->getValue($entity));

		$data = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura'
		);

		$entity->bind($data);

		$this->assertSame($data, $rowProperty->getValue($entity));

		$data = (object) array(
			self::PRIMARY_KEY => 999,
			'name' => 'Sample Name'
		);

		$entity->bind($data);

		$this->assertSame((array) $data, $rowProperty->getValue($entity));
	}

	/**
	 * bind throws exception with wrong data.
	 *
	 * @return  void
	 */
	public function testBindThrowsExceptionWithWrongData()
	{
		$entity = new Entity;

		$exception = false;

		try
		{
			$entity->bind('test');
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);

		$exception = false;

		try
		{
			$entity->bind(111);
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);

		$exception = false;

		try
		{
			$entity->bind(null);
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);

		$exception = false;

		try
		{
			$entity->bind(true);
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);
	}

	/**
	 * bind sets correct id.
	 *
	 * @return  void
	 */
	public function testBindSetsCorrectId()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(0, $idProperty->getValue($entity));

		$data = array(
			self::PRIMARY_KEY => '999',
			'name' => 'Roberto Segura'
		);

		$entity->bind($data);

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * component returns correct value.
	 *
	 * @return  void
	 */
	public function testComponentReturnsCorrectValue()
	{
		$entity = new Entity;

		$this->assertSame('com_tests', $entity->component());

		require_once __DIR__ . '/Stubs/TestsEntityEntity.php';

		$entity = new \TestsEntityEntity;

		$this->assertSame('com_tests', $entity->component());
	}

	/**
	 * Constructor.
	 *
	 * @return  void
	 */
	public function testConstructor()
	{
		$entity = new Entity;
		$entity2 = new Entity('999');
		$entity3 = new Entity(9999);
	}

	/**
	 * Constructor sets correct id.
	 *
	 * @return  void
	 */
	public function testConstructorSetsCorrectId()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(0, $idProperty->getValue($entity));

		$entity = new Entity('999');

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * date returns correct value.
	 *
	 * @return  void
	 */
	public function testDateReturnsCorrectValue()
	{
		$user = $this->getMockBuilder('UserMock')
			->setMethods(array('getTimezone'))
			->getMock();

		$user->expects($this->once())
			->method('getTimezone')
			->willReturn(new \DateTimeZone('GMT'));

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('joomlaUser'))
			->getMock();

		$entity
			->method('joomlaUser')
			->willReturn($user);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('joomlaUser');
		$method->setAccessible(true);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$data = array(
			'id' => 999,
			'date' => '1976-11-16 16:00:00'
		);

		$rowProperty->setValue($entity, $data);

		$this->assertInstanceOf(\JDate::class, $entity->date('date', true));

		\JFactory::$config = new Registry(array('offset' => '+0600'));

		$this->assertInstanceOf(\JDate::class, $entity->date('date', false));
		$this->assertInstanceOf(\JDate::class, $entity->date('date', null));
		$this->assertInstanceOf(\JDate::class, $entity->date('date', 'GMT'));
	}

	/**
	 * date throws exception when date property is empty.
	 *
	 * @return  void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function testDateThrowsExceptionWhenDatePropertyIsEmpty()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$data = array('id' => 999, 'date' => null);

		$rowProperty->setValue($entity, $data);

		$entity->date('date');
	}

	/**
	 * fetch preserves previously assigned data.
	 *
	 * @return  void
	 */
	public function testFetchPreservesPreviouslyAssignedData()
	{
		$row = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Sample name'
		);

		$entity = $this->getLoadableEntityMock($row);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('foo' => 'bar'));

		$entity->fetch();

		$this->assertEquals(array_merge(array('foo' => 'bar'), $row), $rowProperty->getValue($entity));
	}

	/**
	 * fetch loads correct data.
	 *
	 * @return  void
	 */
	public function testFetchLoadsCorrectData()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$row = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Sample name'
		);

		$instances = array(
			Entity::class => array(
				999 => $this->getLoadableEntityMock($row)
			)
		);

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::load(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$this->assertSame($row, $rowProperty->getValue($entity));
	}

	/**
	 * fetchRow throws InvalidEntityData.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\LoadEntityDataError
	 */
	public function testFetchRowThrowsInvalidEntityData()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('load', 'getError'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('load')
			->willReturn(false);

		$tableMock->expects($this->at(1))
			->method('getError')
			->willReturn('En un lugar de La Mancha de cuyo nombre no quiero acordarme');

		$mock = $this->getMockBuilder(Entity::class)
			->setMethods(array('table'))
			->getMock();

		$mock
			->method('table')
			->willReturn($tableMock);

		$mock->fetch();
	}
	/**
	 * fetch throws InvalidEntityData exception for empty data.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\InvalidEntityData
	 */
	public function testFetchThrowsInvalidEntityDataForEmptyData()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = array(
			Entity::class => array(
				999 => $this->getLoadableEntityMock(array())
			)
		);

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::load(999);
	}

	/**
	 * fetch throws InvalidEntityData exception for missing primary key.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\InvalidEntityData
	 */
	public function testFetchThrowsInvalidEntityDataForMissingPrimaryKey()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = array(
			Entity::class => array(
				999 => $this->getLoadableEntityMock(array('name' => 'Sample name'))
			)
		);

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::load(999);
	}

	/**
	 * fetch sets the correct id.
	 *
	 * @return  void
	 */
	public function testFetchSetsCorrectId()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = array(
			Entity::class => array(
				999 => $this->getLoadableEntityMock(array(self::PRIMARY_KEY => 999))
			)
		);

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::load(999);

		$entityReflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * getId returns the correct identifier.
	 *
	 * @return  void
	 */
	public function testGetIdReturnsCorrectIdentifier()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(0, $idProperty->getValue($entity));

		$idProperty->setValue($entity, 999);

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * get returns correct value.
	 *
	 * @return  void
	 */
	public function testGetReturnsCorrectValue()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura',
			'age' => null
		);

		$rowProperty->setValue($entity, $row);

		$this->assertSame(999, $entity->get(self::PRIMARY_KEY));
		$this->assertSame('Roberto Segura', $entity->get('name'));
		$this->assertSame('Roberto Segura', $entity->get('name', 'Isidro Baquero'));
		$this->assertSame(null, $entity->get('age'));
		$this->assertSame(33, $entity->get('age', 33));
	}

	/**
	 * get throws exception for missing property.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetThrowsExceptionForMissingProperty()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura'
		);

		$rowProperty->setValue($entity, $row);

		$entity->get('age');
	}

	/**
	 * getRow forces fetchRow.
	 *
	 * @return  void
	 */
	public function testGetAllForcesFetchRow()
	{
		$row = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura'
		);

		$mock = $this->getMockBuilder(Entity::class)
			->setMethods(array('fetchRow'))
			->getMock();

		$mock->expects($this->once())
			->method('fetchRow')
			->willReturn($row);

		$this->assertSame($row, $mock->all());
	}

	/**
	 * has returns correct value.
	 *
	 * @return  void
	 */
	public function testHasReturnsCorrectValue()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999));

		$this->assertTrue($entity->has(self::PRIMARY_KEY));
		$this->assertFalse($entity->has('name'));
		$this->assertFalse($entity->has('age'));

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'));

		$this->assertTrue($entity->has(self::PRIMARY_KEY));
		$this->assertTrue($entity->has('name'));
		$this->assertFalse($entity->has('age'));
	}

	/**
	 * isLoaded returns correct value.
	 *
	 * @return  void
	 */
	public function testIsLoadedReturnsCorrectValue()
	{
		$entity = new Entity;

		$this->assertFalse($entity->isLoaded());

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($entity, 999);

		$this->assertFalse($entity->isLoaded());

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array());

		$this->assertFalse($entity->isLoaded());

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'));

		$this->assertTrue($entity->isLoaded());
	}

	/**
	 * json returns correct data.
	 *
	 * @return  void
	 */
	public function testJsonReturnsCorrectData()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999));

		$this->assertEquals(array(), $entity->json('json_column'));

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999, 'json_column' => '{"foo":""}'));

		$this->assertEquals(array(), $entity->json('json_column'));

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999, 'json_column' => '{"foo":"0"}'));

		$this->assertEquals(array('foo' => '0'), $entity->json('json_column'));

		$rowProperty->setValue($entity, array(self::PRIMARY_KEY => 999, 'json_column' => '{"foo":"bar"}'));

		$this->assertEquals(array('foo' => 'bar'), $entity->json('json_column'));
	}

	/**
	 * name returns correct value.
	 *
	 * @return  void
	 */
	public function testNameReturnsCorrectValue()
	{
		$entity = new Entity;

		$this->assertSame('entity', $entity->name());

		require_once __DIR__ . '/Stubs/TestsEntityEntity.php';

		$entity = new \TestsEntityEntity;

		$this->assertSame('entity', $entity->name());
	}

	/**
	 * registry returns correct data.
	 *
	 * @return  void
	 */
	public function testRegistryReturnsCorrectData()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$data = array('id' => 999, 'registry' => '{"foo":"bar"}');

		$rowProperty->setValue($entity, $data);

		$this->assertEquals(new Registry('{"foo":"bar"}'), $entity->registry('registry'));
	}

	/**
	 * save returns true.
	 *
	 * @return  void
	 */
	public function testSaveReturnsTrue()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('save'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('save')
			->willReturn(true);

		$mock = $this->getMockBuilder(Entity::class)
			->setMethods(array('table'))
			->getMock();

		$mock
			->method('table')
			->willReturn($tableMock);

		$this->assertTrue($mock->save());
	}

	/**
	 * save throws RuntimeException when errors happen.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testSaveThrowsRuntimeExceptionWhenErrorsHappen()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('save', 'getError'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('save')
			->willReturn(false);

		$tableMock->expects($this->at(1))
			->method('getError')
			->willReturn('En un lugar de La Mancha de cuyo nombre no quiero acordarme');

		$mock = $this->getMockBuilder(Entity::class)
			->setMethods(array('table'))
			->getMock();

		$mock
			->method('table')
			->willReturn($tableMock);

		$mock->save();
	}

	/**
	 * showDate returns correct value.
	 *
	 * @return  void
	 */
	public function testShowDateReturnsCorrectValue()
	{
		$user = $this->getMockBuilder('UserMock')
			->setMethods(array('getTimezone'))
			->getMock();

		$user->expects($this->exactly(2))
			->method('getTimezone')
			->willReturn(new \DateTimeZone('GMT'));

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('joomlaUser'))
			->getMock();

		$entity
			->method('joomlaUser')
			->willReturn($user);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$data = array('id' => 999, 'date' => '1976-11-16 16:05:00');

		$rowProperty->setValue($entity, $data);

		$this->assertSame('Tuesday, 16 November 1976', $entity->showDate('date'));
		$this->assertSame('1976-11-16 16:05:00', $entity->showDate('date', 'DATE_FORMAT_FILTER_DATETIME'));
	}

	/**
	 * unassign unsets row property.
	 *
	 * @return  void
	 */
	public function testUnassignUnsetsRowProperty()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row = array(
			self::PRIMARY_KEY => 999,
			'name' => 'Isidro Baquero'
		);

		$rowProperty->setValue($entity, $row);

		$this->assertSame($row, $rowProperty->getValue($entity));

		$entity->unassign(self::PRIMARY_KEY);

		$this->assertSame(array('name' => 'Isidro Baquero'), $rowProperty->getValue($entity));

		$entity->unassign('name');

		$this->assertSame(array(), $rowProperty->getValue($entity));
	}
}
