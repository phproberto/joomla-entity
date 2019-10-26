<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Exception\SaveException;
use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\Exception\DeleteException;
use Phproberto\Joomla\Entity\Tests\Stubs\EntityWithFakeSave;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;
use Phproberto\Joomla\Entity\Tests\Validation\Traits\Stubs\EntityWithValidation;

/**
 * Base entity tests.
 *
 * @since   1.1.0
 */
class EntityTest extends \TestCaseDatabase
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

		Factory::$session     = $this->getMockSession();
		Factory::$config      = $this->getMockConfig();
		Factory::$application = $this->getMockCmsApp();
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

		Entity::clearAll();
		Entity::$tableMock = null;

		parent::tearDown();
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function bindWithoutOverwriteThrowsExceptionWithWrongData()
	{
		$entity = new Entity;

		$exception = false;

		try
		{
			$entity->bindWithoutOverwrite('test');
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);

		$exception = false;

		try
		{
			$entity->bindWithoutOverwrite(111);
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);

		$exception = false;

		try
		{
			$entity->bindWithoutOverwrite(null);
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);

		$exception = false;

		try
		{
			$entity->bindWithoutOverwrite(true);
		}
		catch (\InvalidArgumentException $e)
		{
			$exception = true;
		}

		$this->assertTrue($exception);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function bindWithoutOverwriteDoesNotOverwriteData()
	{
		$entity = $this->getEntity();

		$entity->bind(
			[
				static::PRIMARY_KEY => 12,
				'sample' => 'original-value'
			]
		);

		$this->assertSame('original-value', $entity->get('sample'));

		$entity->bindWithoutOverwrite(
			[
				static::PRIMARY_KEY => 2222,
				'sample' => 'overwritten',
				'new'    => 'new-value'
			]
		);

		$this->assertSame(12, $entity->get(static::PRIMARY_KEY));
		$this->assertSame('original-value', $entity->get('sample'));
		$this->assertSame('new-value', $entity->get('new'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function defaultsReturnsArray()
	{
		$entity = $this->getEntity();

		$this->assertTrue(is_array($entity->defaults()));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleteReturnsTrueForNoIds()
	{
		$this->assertTrue(Entity::delete([]));
		$this->assertTrue(Entity::delete(['', 0, null]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleteRemovesSpecifiedId()
	{
		$tableMock = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('delete'))
			->getMock();

		$tableMock->expects($this->once())
			->method('delete')
			->with($this->equalTo(45))
			->willReturn(true);

		Entity::$tableMock = $tableMock;

		$this->assertTrue(Entity::delete(45));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleteRemovesSpecifiedIds()
	{
		$tableMock = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('delete'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('delete')
			->with($this->equalTo(23))
			->willReturn(true);

		$tableMock->expects($this->at(1))
			->method('delete')
			->with($this->equalTo(32))
			->willReturn(true);

		Entity::$tableMock = $tableMock;

		$this->assertTrue(Entity::delete([0, '', null, ' ', 23, 32]));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleteThrowsDeleteExceptionOnError()
	{
		$tableMock = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->setMethods(array('delete', 'getError'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('delete')
			->with($this->equalTo(23))
			->willReturn(true);

		$tableMock->expects($this->at(1))
			->method('delete')
			->with($this->equalTo(32))
			->willReturn(false);

		$tableMock->expects($this->once())
			->method('getError')
			->willReturn('Roberto broke it');

		Entity::$tableMock = $tableMock;

		$error = null;

		try
		{
			Entity::delete([23, 32]);
		}
		catch (DeleteException $e)
		{
			$error = $e->getMessage();
		}

		$this->assertContains('Roberto broke it', $error);
	}

	/**
	 * Get a mocked entity.
	 *
	 * @param   array  $row  Row returned by the entity as data
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getEntity($row = array())
	{
		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('primaryKey'))
			->getMock();

		$entity->method('primaryKey')
			->willReturn(static::PRIMARY_KEY);

		$entity->bind($row);

		return $entity;
	}

	/**
	 * Get an entity that simulates data loading.
	 *
	 * @param   integer  $id    Identifier to assign
	 * @param   array    $data  Expected data loaded
	 *
	 * @return  PHPUnit_Framework_MockObject_MockObject
	 */
	private function getLoadableEntityMock($id = null, array $data = array())
	{
		$tableMock = $this->getMockBuilder('MockedTable')
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
			->disableOriginalConstructor()
			->setMethods(array('table', 'primaryKey'))
			->getMock();

		$mock
			->method('table')
			->willReturn($tableMock);

		$mock->method('primaryKey')
			->willReturn(static::PRIMARY_KEY);

		if ($id)
		{
			$reflection = new \ReflectionClass($mock);
			$idProperty = $reflection->getProperty('id');
			$idProperty->setAccessible(true);
			$idProperty->setValue($mock, $id);
		}

		return $mock;
	}

	/**
	 * Dates provider.
	 *
	 * @return  array
	 *
	 * @since   1.8
	 */
	public function hasEmptyDateProvider()
	{
		return [
			// Date, expectedResult
			[null, true],
			['1976-11-16', false],
			['', true],
			['1976-11-16 00:00:00', false],
			[' ', true],
			['1989-5-20 12:00:01', false],
			['0', true],
			['0000-00-00', true],
			['2000-00-00', false],
			['0000-00-00 00:00:00', true],
			['1971-01-01', true],
			['1971-01-01 00:00:00', true],
			['1971-01-01 00:00:00 ', true]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  hasEmptyDateProvider
	 *
	 * @param   string   $date            Date to test
	 * @param   boolean  $expectedResult  Expected result for hasEmptyDate()
	 *
	 * @return void
	 *
	 * @since   1.8
	 */
	public function hasEmptyDateReturnExpectedValue($date, bool $expectedResult)
	{
		$entity = $this->getEntity();
		$entity->assign('test-date', $date);

		$this->assertSame($expectedResult, $entity->hasEmptyDate('test-date'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadFromDataReturnsUnloadedEntityOnLoadFailure()
	{
		$tableMock = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('load'))
			->getMock();

		$tableMock->expects($this->once())
			->method('load')
			->willReturn(false);

		Entity::$tableMock = $tableMock;

		$entity = Entity::loadFromData([]);

		$this->assertInstanceOf(Entity::class, $entity);
		$this->assertFalse($entity->isLoaded());

		$entity2 = Entity::loadFromData(['id' => 23]);

		$this->assertInstanceOf(Entity::class, $entity2);
		$this->assertFalse($entity2->isLoaded());
		$this->assertNotSame($entity, $entity2);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadFromDataReturnsLoadedEntity()
	{
		$data = [
			'name' => 'Grijander'
		];

		$tableMock = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('getProperties', 'load'))
			->getMock();

		$tableMock->expects($this->once())
			->method('load')
			->with($this->equalTo($data))
			->willReturn(true);

		$tableMock->expects($this->once())
			->method('getProperties')
			->willReturn(['id' => 999, 'name' => 'Grijander']);

		Entity::$tableMock = $tableMock;

		$entity = Entity::loadFromData($data);

		$this->assertTrue($entity->isLoaded());
		$this->assertSame(999, $entity->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tableNameWillReturnUcfirstName()
	{
		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('ucfirstName'))
			->getMock();

		$entity->expects($this->once())
			->method('ucfirstName')
			->willReturn('Tabata_Roberto');

		$this->assertSame('Tabata_Roberto', $entity->tableName());
	}

	/**
	 * Test magic __get method calls get.
	 *
	 * @return  void
	 */
	public function testMagicGetReturnsGet()
	{
		$entity = $this->getMockBuilder(Entity::class)
			->disableOriginalConstructor()
			->setMethods(array('get'))
			->getMock();

		$entity->expects($this->once())
			->method('get')
			->with($this->equalTo('property'))
			->willReturn('fake value');

		$this->assertSame('fake value', $entity->property);
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

		$this->assertSame([], $rowProperty->getValue($entity));

		$entity->assign('name', 'Sample name');

		$this->assertSame(['name' => 'Sample name'], $rowProperty->getValue($entity));
	}

	/**
	 * Assign sets correct id.
	 *
	 * @return  void
	 */
	public function testAssignSetsCorrectId()
	{
		$entity = $this->getEntity(array(static::PRIMARY_KEY => '666'));

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(666, $idProperty->getValue($entity));

		$entity->assign(static::PRIMARY_KEY, '999');

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * bind sets correct object data.
	 *
	 * @return  void
	 */
	public function testBindSetsCorrectData()
	{
		$entity = $this->getEntity();

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$this->assertSame(array(), $rowProperty->getValue($entity));

		$data = array(
			static::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura'
		);

		$entity->bind($data);

		$this->assertSame($data, $rowProperty->getValue($entity));

		$data = (object) array(
			static::PRIMARY_KEY => 999,
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
		$entity = $this->getEntity();

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(0, $idProperty->getValue($entity));

		$data = array(
			static::PRIMARY_KEY => '999',
			'name' => 'Roberto Segura'
		);

		$entity->bind($data);

		$this->assertSame(999, $idProperty->getValue($entity));
	}

	/**
	 * columnAlias returns sent column if not alias is set.
	 *
	 * @return  void
	 */
	public function testColumnAliasReturnsSentColumnIfNoAliasIsSet()
	{
		$tableMock = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('getColumnAlias'))
			->getMock();

		$tableMock->expects($this->once())
			->method('getColumnAlias')
			->willReturn('published');

		$entity = $this->getMockBuilder(Entity::class)
			->disableOriginalConstructor()
			->setMethods(array('table'))
			->getMock();

		$entity
			->method('table')
			->willReturn($tableMock);

		$this->assertSame('published', $entity->columnAlias('published'));
	}

	/**
	 * columnAlias returns entity alias if set.
	 *
	 * @return  void
	 */
	public function testColumnAliasReturnsEntityAliasIfSet()
	{
		$entity = $this->getMockBuilder(Entity::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAliases'))
			->getMock();

		$entity->expects($this->once())
			->method('columnAliases')
			->willReturn(array('published' => 'entityValue'));

		$this->assertSame('entityValue', $entity->columnAlias('published'));
	}

	/**
	 * columnAliases returns array.
	 *
	 * @return  void
	 */
	public function testColumnAliasesReturnsAnArray()
	{
		$entity = new Entity;

		$this->assertSame(true, is_array($entity->columnAliases()));
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
	 * create returns saved instance.
	 *
	 * @return  void
	 */
	public function testCreateReturnsSavedInstance()
	{
		$data = array(
			self::PRIMARY_KEY => 34,
			'name'            => 'My name',
			'age'             => 25
		);

		$entity = EntityWithFakeSave::create($data);

		$this->assertTrue($entity->saved);
		$this->assertNotSame($data, $entity->savedData);

		unset($data[self::PRIMARY_KEY]);

		$this->assertSame($data, $entity->savedData);
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
			->setMethods(array('juser'))
			->getMock();

		$entity
			->method('juser')
			->willReturn($user);

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$data = array(
			'id' => 999,
			'date' => '1976-11-16 16:00:00'
		);

		$rowProperty->setValue($entity, $data);

		$this->assertInstanceOf(\JDate::class, $entity->date('date', true));

		Factory::$config = new Registry(array('offset' => '+0600'));

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
		$dataFromDb = array(
			static::PRIMARY_KEY => 999,
			'name' => 'Sample name'
		);

		$assignedData = array(
			'foo' => 'bar',
			'name' => 'Modified name'
		);

		$entity = $this->getLoadableEntityMock(999, $dataFromDb);

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 43);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, $assignedData);

		$entity->fetch();

		$this->assertEquals(array_merge($dataFromDb, $assignedData), $rowProperty->getValue($entity));
		$this->assertSame($assignedData['name'], $entity->get('name'));
	}

	/**
	 * load loads correct data.
	 *
	 * @return  void
	 */
	public function testLoadLoadsCorrectData()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$row = array(
			static::PRIMARY_KEY => 999,
			'name' => 'Sample name'
		);

		$instances = array(
			Entity::class => array(
				999 => $this->getLoadableEntityMock(999, $row)
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
	 * fetch throws InvalidEntityData exception for empty data.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\InvalidEntityData
	 */
	public function testFetchRowThrowsExceptionForEmptyData()
	{
		$entity = $this->getLoadableEntityMock(999, array());

		$reflection = new \ReflectionClass(Entity::class);

		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * fetchRow throws InvalidEntityData exception for missing primary key.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\InvalidEntityData
	 */
	public function testFetchRowThrowsExceptionForMissingPrimaryKey()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * fetchRow throws exception when table load fails.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\LoadEntityDataError
	 */
	public function testFetchRowThrowsExceptionWhenTableLoadFails()
	{
		$tableMock = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('load', 'getError'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('load')
			->willReturn(false);

		$tableMock->expects($this->at(1))
			->method('getError')
			->willReturn('En un lugar de la mancha de cuyo nombre no quiero acordarme');

		$entity = $this->getMockBuilder(Entity::class)
			->disableOriginalConstructor()
			->setMethods(array('table'))
			->getMock();

		$entity
			->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * fetchRow throws exception when primary key is not in the loaded data.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\InvalidEntityData
	 */
	public function testFetchRowThrowsExceptionWhenPrimaryKeyIsNotInTheLoadedData()
	{
		$entity = $this->getLoadableEntityMock(999, array('title' => 'Sample title'));

		$reflection = new \ReflectionClass(Entity::class);

		$method = $reflection->getMethod('fetchRow');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * fromData returns entity with data binded.
	 *
	 * @return  void
	 */
	public function testFromDataReturnsEntityWithDataBinded()
	{
		$data = array(
			self::PRIMARY_KEY => 333,
			'name' => 'Hello world'
		);

		$entity = Entity::fromData($data);

		$this->assertInstanceOf(Entity::class, $entity);
		$this->assertSame($data, $entity->all());
	}

	/**
	 * load sets the correct id.
	 *
	 * @return  void
	 */
	public function testLoadSetsCorrectId()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$instances = array(
			Entity::class => array(
				999 => $this->getLoadableEntityMock(999, array(static::PRIMARY_KEY => 999))
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
	 * id returns the correct identifier.
	 *
	 * @return  void
	 */
	public function testGetIdReturnsCorrectIdentifier()
	{
		$entity = new Entity;

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(0, $entity->id());

		$idProperty->setValue($entity, 999);

		$this->assertSame(999, $entity->id());
	}

	/**
	 * get returns correct value.
	 *
	 * @return  void
	 */
	public function testGetReturnsCorrectValue()
	{
		$row = array(
			static::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura',
			'age' => null
		);

		$entity = $this->getEntity($row);

		$this->assertSame(999, $entity->get(static::PRIMARY_KEY));
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
		$entity = new Entity(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row = array(
			static::PRIMARY_KEY => 999,
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
			static::PRIMARY_KEY => 999,
			'name' => 'Roberto Segura'
		);

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('fetchRow'))
			->getMock();

		$entity->expects($this->once())
			->method('fetchRow')
			->willReturn($row);

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($entity, 999);

		$this->assertSame($row, $entity->all());
	}

	/**
	 * has returns correct value.
	 *
	 * @return  void
	 */
	public function testHasReturnsCorrectValue()
	{
		$entity = $this->getEntity(array(static::PRIMARY_KEY => 999));

		$this->assertTrue($entity->has(static::PRIMARY_KEY));
		$this->assertFalse($entity->has('name'));
		$this->assertFalse($entity->has('age'));

		$entity = $this->getEntity(array(static::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'));

		$this->assertTrue($entity->has(static::PRIMARY_KEY));
		$this->assertTrue($entity->has('name'));
		$this->assertFalse($entity->has('age'));
	}

	/**
	 * hasEmpty returns correct value.
	 *
	 * @return  void
	 */
	public function testHasEmptyReturnsCorrectValue()
	{
		$entity = $this->getEntity(
			array(
				static::PRIMARY_KEY => 999,
				'name' => 'Roberto Segura',
				'empty_int' => 0,
				'empty_array' => array(),
				'empty_null' => null,
				'empty_false' => false,
				'empty_string' => '',
				'not_empty_int' => 1,
				'not_empty_array' => array(0),
				'not_empty_boolean' => true,
				'not_empty_float' => 0.123
			)
		);

		$this->assertFalse($entity->hasEmpty('name'));
		$this->assertFalse($entity->hasEmpty('unexistent'));
		$this->assertTrue($entity->hasEmpty('empty_int'));
		$this->assertFalse($entity->hasEmpty('not_empty_int'));
		$this->assertTrue($entity->hasEmpty('empty_array'));
		$this->assertFalse($entity->hasEmpty('not_empty_array'));
		$this->assertTrue($entity->hasEmpty('empty_null'));
		$this->assertFalse($entity->hasEmpty('not_empty_boolean'));
		$this->assertTrue($entity->hasEmpty('empty_false'));
		$this->assertFalse($entity->hasEmpty('not_empty_float'));
		$this->assertTrue($entity->hasEmpty('empty_string'));
	}

	/**
	 * hasNotEmpty returns correct value.
	 *
	 * @return  void
	 */
	public function testHasNotEmptyReturnsCorrectValue()
	{
		$entity = $this->getEntity(
			array(
				static::PRIMARY_KEY => 999,
				'name' => 'Roberto Segura',
				'empty_int' => 0,
				'empty_array' => array(),
				'empty_null' => null,
				'empty_false' => false,
				'empty_string' => '',
				'not_empty_int' => 1,
				'not_empty_array' => array(0),
				'not_empty_boolean' => true,
				'not_empty_float' => 0.123
			)
		);

		$this->assertTrue($entity->hasNotEmpty('name'));
		$this->assertFalse($entity->hasNotEmpty('unexistent'));
		$this->assertFalse($entity->hasNotEmpty('empty_int'));
		$this->assertTrue($entity->hasNotEmpty('not_empty_int'));
		$this->assertFalse($entity->hasNotEmpty('empty_array'));
		$this->assertTrue($entity->hasNotEmpty('not_empty_array'));
		$this->assertFalse($entity->hasNotEmpty('empty_null'));
		$this->assertTrue($entity->hasNotEmpty('not_empty_boolean'));
		$this->assertFalse($entity->hasNotEmpty('empty_false'));
		$this->assertTrue($entity->hasNotEmpty('not_empty_float'));
		$this->assertFalse($entity->hasNotEmpty('empty_string'));
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

		$rowProperty->setValue($entity, array(static::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'));

		$this->assertTrue($entity->isLoaded());
	}

	/**
	 * json returns correct data.
	 *
	 * @return  void
	 */
	public function testJsonReturnsCorrectData()
	{
		$entity = $this->getEntity(array(static::PRIMARY_KEY => 999, 'json_column' => ''));

		$this->assertEquals(array(), $entity->json('json_column'));

		$entity = $this->getEntity(array(static::PRIMARY_KEY => 999, 'json_column' => '{"foo":""}'));

		$this->assertEquals(array(), $entity->json('json_column'));

		$entity = $this->getEntity(array(static::PRIMARY_KEY => 999, 'json_column' => '{"foo":"0"}'));

		$this->assertEquals(array('foo' => '0'), $entity->json('json_column'));

		$entity = $this->getEntity(array(static::PRIMARY_KEY => 999, 'json_column' => '{"foo":"bar"}'));

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
	 * @test
	 *
	 * @return void
	 */
	public function saveAppliesDefaults()
	{
		$defaults = [
			'sample' => 'default'
		];

		$data = array(
			'name' => 'Anibal Sánchez'
		);

		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('save'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('save')
			->willReturn(true);

		foreach ($defaults as $property => $value)
		{
			$tableMock->{$property} = $value;
		}

		foreach ($data as $property => $value)
		{
			$tableMock->{$property} = $value;
		}

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('table', 'defaults'))
			->getMock();

		$entity
			->method('table')
			->willReturn($tableMock);

		$entity
			->method('defaults')
			->willReturn($defaults);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, $data);

		$newEntity = $entity->save();

		$this->assertInstanceOf(get_class($entity), $newEntity);

		foreach ($data as $property => $value)
		{
			$this->assertSame($data[$property], $newEntity->{$property});
		}

		foreach ($defaults as $property => $value)
		{
			$this->assertSame($defaults[$property], $newEntity->{$property});
		}
	}

	/**
	 * save returns saved instance.
	 *
	 * @return  void
	 */
	public function testSaveReturnsSavedInstance()
	{
		$data = array(
			self::PRIMARY_KEY => 999,
			'name'            => 'Anibal Sánchez'
		);

		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('save'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('save')
			->willReturn(true);

		foreach ($data as $property => $value)
		{
			$tableMock->{$property} = $value;
		}

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('table'))
			->getMock();

		$entity
			->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, $data);

		$newEntity = $entity->save();

		$this->assertInstanceOf(get_class($entity), $newEntity);

		foreach ($data as $property => $value)
		{
			$this->assertSame($data[$property], $newEntity->{$property});
		}
	}

	/**
	 * save throws RuntimeException when errors happen.
	 *
	 * @return  void
	 *
	 * @expectedException \Phproberto\Joomla\Entity\Exception\SaveException
	 */
	public function testSaveThrowsExceptionWhenTableErrorsHappen()
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
	 * save throws exception when validable entities throw validation exceptions.
	 *
	 * @return  void
	 */
	public function testSaveThrowsExceptionWhenValidableEntitiesThrowValidationExceptions()
	{
		$entity = $this->getMockBuilder(EntityWithValidation::class)
			->disableOriginalConstructor()
			->setMethods(array('validate'))
			->getMock();

		$entity->expects($this->once())
			->method('validate')
			->will($this->throwException(new ValidationException('Validation error happened')));

		try
		{
			$entity->save();
		}
		catch (ValidationException $exception)
		{
		}

		$this->assertInstanceOf(ValidationException::class, $exception);
		$this->assertTrue(strlen($exception->getMessage()) > 0);
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
			->setMethods(array('juser'))
			->getMock();

		$entity
			->method('juser')
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
	 * Dates provider.
	 *
	 * @return  array
	 *
	 * @since   1.8
	 */
	public function showNotEmptyDateProvider()
	{
		return [
			// Date, expectedResult
			[null, 'DATE_FORMAT_LC1', ''],
			['1976-11-16', 'DATE_FORMAT_LC1', 'Tuesday, 16 November 1976'],
			['1989-5-20 12:00:01', 'd-m-Y', '20-05-1989']
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  showNotEmptyDateProvider
	 *
	 * @param   string  $date      Date to test
	 * @param   string  $format    Desired date format
	 * @param   string  $expected  Expected formated date
	 *
	 * @return void
	 *
	 * @since   1.8
	 */
	public function showNotEmptyDateReturnsExpectedValue($date, $format, $expected)
	{
		$user = $this->getMockBuilder('UserMock')
			->setMethods(array('getTimezone'))
			->getMock();

		$user->method('getTimezone')
			->willReturn(new \DateTimeZone('GMT'));

		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('juser'))
			->getMock();

		$entity
			->method('juser')
			->willReturn($user);

		$entity->assign('test-date', $date);

		$this->assertSame($expected, $entity->showNotEmptyDate('test-date', $format));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tablePrefixReturnsEntityPrefixForNonNamespacedEntities()
	{
		require_once __DIR__ . '/Stubs/TestsEntityEntity.php';

		$entity = new \TestsEntityEntity;

		$this->assertSame('TestsTable', $entity->tablePrefix());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tablePrefixReturnsLatestParentNamespaceForNamespacedEntities()
	{
		$entity = new Entity;

		$this->assertSame('StubsTable', $entity->tablePrefix());

		$entity = new \Phproberto\Joomla\Entity\Tests\Stubs\Entity\MyEntity;

		$this->assertSame('StubsTable', $entity->tablePrefix());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function tableReturnsExpectedTable()
	{
		$customDb = clone Factory::getDbo();

		$this->assertNotSame($customDb, Factory::getDbo());

		$entity = new Entity;

		$config = [
			'dbo' => $customDb
		];

		$table = $entity->table('Content', 'JTable', $config);

		$reflection = new \ReflectionClass($table);
		$dbProperty = $reflection->getProperty('_db');
		$dbProperty->setAccessible(true);

		$this->assertInstanceOf(Table::class, $table);
		$this->assertSame($customDb, $dbProperty->getValue($table));
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
			static::PRIMARY_KEY => 999,
			'name' => 'Isidro Baquero'
		);

		$rowProperty->setValue($entity, $row);

		$this->assertSame($row, $rowProperty->getValue($entity));

		$entity->unassign(static::PRIMARY_KEY);

		$this->assertSame(array('name' => 'Isidro Baquero'), $rowProperty->getValue($entity));

		$entity->unassign('name');

		$this->assertSame(array(), $rowProperty->getValue($entity));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function ucfirstNameDefaultsToEntityName()
	{
		$entity = $this->getMockBuilder(Entity::class)
			->setMethods(array('name'))
			->getMock();

		$entity->expects($this->once())
			->method('name')
			->willReturn('tabata_roberto');

		$this->assertSame('Tabata_Roberto', $entity->ucfirstName());
	}
}
