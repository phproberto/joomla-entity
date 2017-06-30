<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;

/**
 * Base entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Name of the primary key
	 *
	 * @const
	 */
	const PRIMARY_KEY = 'id';

	/**
	 * Get an entity that simulates data loading.
	 *
	 * @param   array   $data  Expected data loaded
	 *
	 * @return  PHPUnit_Framework_MockObject_MockObject
	 */
	private function getLoadableEntityMock(array $data = [])
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
			->setMethods(array('getTable'))
			->getMock();

		$mock
			->method('getTable')
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

		$this->assertSame(['name' => 'Sample name'], $rowProperty->getValue($entity));
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

		$data = [self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'];

		$entity->bind($data);

		$this->assertSame($data, $rowProperty->getValue($entity));
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

		$data = [self::PRIMARY_KEY => '999', 'name' => 'Roberto Segura'];

		$entity->bind($data);

		$this->assertSame(999, $idProperty->getValue($entity));
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
	 * fetch loads correct data.
	 *
	 * @return  void
	 */
	public function testFetchLoadsCorrectData()
	{
		$reflection = new \ReflectionClass(Entity::class);
		$instancesProperty = $reflection->getProperty('instances');
		$instancesProperty->setAccessible(true);

		$row = [self::PRIMARY_KEY => 999, 'name' => 'Sample name'];

		$instances = [
			Entity::class => [
				999 => $this->getLoadableEntityMock($row)
			]
		];

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::fetch(999);

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
			->setMethods(array('getTable'))
			->getMock();

		$mock
			->method('getTable')
			->willReturn($tableMock);

		$mock->loadRow();
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

		$instances = [
			Entity::class => [
				999 => $this->getLoadableEntityMock([])
			]
		];

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::fetch(999);
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

		$instances = [
			Entity::class => [
				999 => $this->getLoadableEntityMock(['name' => 'Sample name'])
			]
		];

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::fetch(999);
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

		$instances = [
			Entity::class => [
				999 => $this->getLoadableEntityMock([self::PRIMARY_KEY => 999])
			]
		];

		$instancesProperty->setValue(Entity::class, $instances);

		$entity = Entity::fetch(999);

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

		$row = [self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'];

		$rowProperty->setValue($entity, $row);

		$this->assertSame(999, $entity->get(self::PRIMARY_KEY));
		$this->assertSame('Roberto Segura', $entity->get('name'));
		$this->assertSame('Roberto Segura', $entity->get('name', 'Isidro Baquero'));
		$this->assertSame(null, $entity->get('age'));
		$this->assertSame(33, $entity->get('age', 33));
	}

	/**
	 * getRow forces fetchRow.
	 *
	 * @return  void
	 */
	public function testGetRowForcesFetchRow()
	{
		$row = [self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura'];

		$mock = $this->getMockBuilder(Entity::class)
			->setMethods(array('fetchRow'))
			->getMock();

		$mock->expects($this->once())
			->method('fetchRow')
			->willReturn($row);

		$this->assertSame($row, $mock->getRow());
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

		$rowProperty->setValue($entity, [self::PRIMARY_KEY => 999]);

		$this->assertTrue($entity->has(self::PRIMARY_KEY));
		$this->assertFalse($entity->has('name'));
		$this->assertFalse($entity->has('age'));

		$rowProperty->setValue($entity, [self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura']);

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

		$rowProperty->setValue($entity, []);

		$this->assertFalse($entity->isLoaded());

		$rowProperty->setValue($entity, [self::PRIMARY_KEY => 999, 'name' => 'Roberto Segura']);

		$this->assertTrue($entity->isLoaded());
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
			->setMethods(array('getTable'))
			->getMock();

		$mock
			->method('getTable')
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
			->setMethods(array('getTable'))
			->getMock();

		$mock
			->method('getTable')
			->willReturn($tableMock);

		$mock->save();
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

		$row = [self::PRIMARY_KEY => 999, 'name' => 'Isidro Baquero'];
		$rowProperty->setValue($entity, $row);

		$this->assertSame($row, $rowProperty->getValue($entity));

		$entity->unassign(self::PRIMARY_KEY);

		$this->assertSame(['name' => 'Isidro Baquero'], $rowProperty->getValue($entity));

		$entity->unassign('name');

		$this->assertSame([], $rowProperty->getValue($entity));
	}
}
