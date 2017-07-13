<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Phproberto\Joomla\Entity\Tests\Stubs\Entity;
use Phproberto\Joomla\Entity\EntityCollection;

/**
 * Entity collection tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityCollectionTest extends \TestCase
{
	/**
	 * Constructor sets entities.
	 *
	 * @return  void
	 */
	public function testConstructorSetsEntities()
	{
		$collection = new EntityCollection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(), $entitiesProperty->getValue($collection));

		$entities = array(
			new Entity(1000),
			new Entity(1001)
		);

		$collection = new EntityCollection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertEquals(
			array(
				1000 => new Entity(1000),
				1001 => new Entity(1001)
			),
			$entitiesProperty->getValue($collection)
		);
	}

	/**
	 * add adds a new entity.
	 *
	 * @return  void
	 */
	public function testAddAddsNewEntity()
	{
		$collection = new EntityCollection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(), $entitiesProperty->getValue($collection));

		$entity = new Entity(1000);

		$this->assertTrue($collection->add($entity));

		$this->assertSame(array(1000 => $entity), $entitiesProperty->getValue($collection));

		$entity1 = new Entity(1001);

		$this->assertTrue($collection->add($entity1));

		$this->assertSame(array(1000 => $entity, 1001 => $entity1), $entitiesProperty->getValue($collection));
	}

	/**
	 * add throws exception when entity has no id.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 *
	 */
	public function testAddThrowsExceptionWhenEntityHasNoId()
	{
		$collection = new EntityCollection;

		$collection->add(new Entity);
	}

	/**
	 * add does not overwrite entity.
	 *
	 * @return  void
	 */
	public function testAddDoesNotOverwriteEntity()
	{
		$collection = new EntityCollection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(), $entitiesProperty->getValue($collection));

		$entity = new Entity(1000);
		$entity2 = new Entity(1000);
		$entity3 = new Entity(1001);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$expectedRow = array('id' => 1000, 'name' => 'Roberto Segura');

		$rowProperty->setValue($entity, $expectedRow);

		$this->assertTrue($collection->add($entity));

		$this->assertSame(array(1000 => $entity), $entitiesProperty->getValue($collection));

		$this->assertFalse($collection->add($entity2));

		$this->assertNotSame(array(1000 => $entity2), $entitiesProperty->getValue($collection));
	}

	/**
	 * clear empties entities array.
	 *
	 * @return  void
	 */
	public function testClearEmptiesEntitiesArray()
	{
		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(3, count($entitiesProperty->getValue($collection)));

		$collection->clear();

		$this->assertSame(array(), $entitiesProperty->getValue($collection));
	}

	/**
	 * count returns correct value.
	 *
	 * @return  void
	 */
	public function testCountReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(0, $collection->count());

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001)));

		$this->assertSame(2, $collection->count());

		$entitiesProperty->setValue(
			$collection,
			array(
				1000 => new Entity(1000),
				1001 => new Entity(1001),
				1002 => new Entity(1002)
			)
		);

		$this->assertSame(3, $collection->count());
	}

	/**
	 * current returns correct value.
	 *
	 * @return  void
	 */
	public function testCurrentReturnsCorrectValue()
	{
		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

		foreach ($collection as $entity)
		{
			$this->assertSame($collection->current(), $entity);
		}

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$entitiesProperty->setValue($collection, $entities);

		$this->assertEquals(new Entity(1000), $collection->current());

		while (key($entities) !== 1001)
		{
		    next($entities);
		}

		$entitiesProperty->setValue($collection, $entities);

		$this->assertEquals(new Entity(1001), $collection->current());
	}

	/**
	 * getAll returns correct value.
	 *
	 * @return  void
	 */
	public function testGetAllReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$this->assertSame(array(), $collection->getAll());

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001)
		);

		$collection = new EntityCollection($entities);

		$this->assertSame($entities, $collection->getAll());
	}

	/**
	 * get retrieves correct entity.
	 *
	 * @return  void
	 */
	public function testGetRetrievesCorrectEntity()
	{
		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001)));

		$this->assertEquals(new Entity(1000), $collection->get(1000));
		$this->assertEquals(new Entity(1001), $collection->get(1001));
	}

	/**
	 * get throws exception when element is not present.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetThrowsExceptionForMissingElement()
	{
		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001)));

		$collection->get(1002);
	}

	/**
	 * has returns correct vlaue.
	 *
	 * @return  void
	 */
	public function testHasReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$this->assertFalse($collection->has(1000));
		$this->assertFalse($collection->has(1001));
		$this->assertFalse($collection->has(1002));

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001)));

		$this->assertTrue($collection->has(1000));
		$this->assertFalse($collection->has(1002));
		$this->assertTrue($collection->has(1001));
	}

	/**
	 * ids returns correct identifiers.
	 *
	 * @return  void
	 */
	public function testIdsReturnsCorrectIdentifiers()
	{
		$collection = new EntityCollection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(), $collection->ids());

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001)));

		$this->assertSame(array(1000, 1001), $collection->ids());

		$entitiesProperty->setValue(
			$collection,
			array(
				1000 => new Entity(1000),
				1002 => new Entity(1002),
				1001 => new Entity(1001)
			)
		);

		$this->assertSame(array(1000, 1002, 1001), $collection->ids());
	}

	/**
	 * intersect returns correct value.
	 *
	 * @return  void
	 */
	public function testIntersectReturnsCorrectValue()
	{
		$collection1 = new EntityCollection;
		$collection2 = new EntityCollection;

		$result = $collection1->intersect($collection2);

		$reflection = new \ReflectionClass($result);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertEquals(array(), $entitiesProperty->getValue($result));

		$collection1 = new EntityCollection(array(new Entity(1000), new Entity(1001)));
		$collection2 = new EntityCollection(array(new Entity(1002), new Entity(1000)));

		$result = $collection1->intersect($collection2);

		$expectetdEntities = array(
			1000 => new Entity(1000)
		);

		$this->assertEquals($expectetdEntities, $entitiesProperty->getValue($result));
		$this->assertSame(array(1000), array_keys($entitiesProperty->getValue($result)));

		// Ensure that source entities aren't modified
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection1)));
		$this->assertSame(array(1002, 1000), array_keys($entitiesProperty->getValue($collection2)));

		$collection1 = new EntityCollection(array(new Entity(999), new Entity(1000), new Entity(1001)));
		$collection2 = new EntityCollection(array(new Entity(1001), new Entity(1000), new Entity(1002),));

		$result = $collection1->intersect($collection2);

		$expectetdEntities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001)
		);

		$this->assertEquals($expectetdEntities, $entitiesProperty->getValue($result));
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($result)));

		// Ensure that source entities aren't modified
		$this->assertSame(array(999, 1000, 1001), array_keys($entitiesProperty->getValue($collection1)));
		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection2)));
	}

	/**
	 * isEmpty returns correct value.
	 *
	 * @return  void
	 */
	public function testIsEmptyReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertTrue($collection->isEmpty());

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001)));

		$this->assertFalse($collection->isEmpty());

		$entitiesProperty->setValue($collection, array());

		$this->assertTrue($collection->isEmpty());

		$entitiesProperty->setValue(
			$collection,
			array(
				1000 => new Entity(1000),
				1002 => new Entity(1002),
				1001 => new Entity(1001)
			)
		);

		$this->assertFalse($collection->isEmpty());
	}

	/**
	 * key returns correct value.
	 *
	 * @return  void
	 */
	public function testKeyReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$this->assertSame(null, $collection->key());

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

		foreach ($collection as $entity)
		{
			$this->assertSame($collection->key(), $entity->getId());
		}

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$entitiesProperty->setValue($collection, $entities);

		$this->assertSame(1000, $collection->key());

		while (key($entities) !== 1001)
		{
			next($entities);
		}

		$entitiesProperty->setValue($collection, $entities);

		$this->assertSame(1001, $collection->key());
	}

	/**
	 * ksort orders entities.
	 *
	 * @return  void
	 */
	public function testKsortOrdersEntities()
	{
		$entities = array(1001 => new Entity(1001), 1000 => new Entity(1000), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection)));

		$collection->ksort();

		$this->assertSame(array(1000, 1001, 1002), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * krsort orders entities.
	 *
	 * @return  void
	 */
	public function testKrsortOrdersEntities()
	{
		$entities = array(1001 => new Entity(1001), 1000 => new Entity(1000), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection)));

		$collection->krsort();

		$this->assertSame(array(1002, 1001, 1000), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * Merge returns correct collection.
	 *
	 * @return  void
	 */
	public function testMergeReturnsCorrectCollection()
	{
		$collection1 = new EntityCollection;
		$collection2 = new EntityCollection;

		$mergedCollection = $collection1->merge($collection2);

		$reflection = new \ReflectionClass($mergedCollection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertEquals(array(), $entitiesProperty->getValue($mergedCollection));

		$collection1 = new EntityCollection(array(new Entity(1000), new Entity(1001)));
		$collection2 = new EntityCollection(array(new Entity(1002), new Entity(1003)));

		$mergedCollection = $collection1->merge($collection2);

		$expectetdEntities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002),
			1003 => new Entity(1003)
		);

		$this->assertEquals($expectetdEntities, $entitiesProperty->getValue($mergedCollection));
		$this->assertSame(array(1000, 1001, 1002, 1003), array_keys($entitiesProperty->getValue($mergedCollection)));

		// Ensure that source entities aren't modified
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection1)));
		$this->assertSame(array(1002, 1003), array_keys($entitiesProperty->getValue($collection2)));

		$collection1 = new EntityCollection(array(new Entity(1000), new Entity(1001)));
		$collection2 = new EntityCollection(array(new Entity(1002), new Entity(1003)));

		$mergedCollection = $collection2->merge($collection1);

		$expectetdEntities = array(
			1002 => new Entity(1002),
			1003 => new Entity(1003),
			1000 => new Entity(1000),
			1001 => new Entity(1001)
		);

		$this->assertEquals($expectetdEntities, $entitiesProperty->getValue($mergedCollection));
		$this->assertSame(array(1002, 1003, 1000, 1001), array_keys($entitiesProperty->getValue($mergedCollection)));

		// Ensure that source entities aren't modified
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection1)));
		$this->assertSame(array(1002, 1003), array_keys($entitiesProperty->getValue($collection2)));
	}

	/**
	 * next returns correct value.
	 *
	 * @return  void
	 */
	public function testNextReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$this->assertSame(false, $collection->next());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		foreach ($collection as $entity)
		{
			if ($entity->getId() !== 1002)
			{
				$this->assertSame($collection->next(), $entities[$entity->getId() + 1]);
			}
		}

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$collection = new EntityCollection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

		$entitiesProperty->setValue($collection, $entities);

		$this->assertSame($entities[1001], $collection->next());

		while (key($entities) !== 1001)
		{
			next($entities);
		}

		$entitiesProperty->setValue($collection, $entities);

		$this->assertSame($entities[1002], $collection->next());
	}

	/**
	 * remove removes entity.
	 *
	 * @return  void
	 */
	public function testRemoveRemovesEntity()
	{
		$collection = new EntityCollection;

		$this->assertSame(false, $collection->remove(1000));

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		$this->assertSame(false, $collection->remove(1005));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(true, $collection->remove(1001));
		$this->assertEquals(array(1000 => new Entity(1000), 1002 => new Entity(1002)), $entitiesProperty->getValue($collection));

		$this->assertSame(true, $collection->remove(1000));
		$this->assertEquals(array(1002 => new Entity(1002)), $entitiesProperty->getValue($collection));

		$this->assertSame(true, $collection->remove(1002));
		$this->assertEquals(array(), $entitiesProperty->getValue($collection));
	}

	/**
	 * rewind returns correct value.
	 *
	 * @return  void
	 */
	public function testRewindReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$this->assertSame(false, $collection->rewind());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		$this->assertSame($entities[1000], $collection->rewind());

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		while (key($entities) !== 1001)
		{
			next($entities);
		}

		$entitiesProperty->setValue($collection, $entities);

		$this->assertSame(1001, key($entitiesProperty->getValue($collection)));
		$this->assertEquals(new Entity(1000), $collection->rewind());
	}

	/**
	 * toObjects returns correct data.
	 *
	 * @return  void
	 */
	public function testToObjectsReturnsCorrectData()
	{
		$collection = new EntityCollection;

		$this->assertEquals(array(), $collection->toObjects());

		$entity1 = new Entity(1000);
		$entity2 = new Entity(1001);

		$reflection = new \ReflectionClass($entity1);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row1 = array('id' => 1000, 'name' => 'Vicente Monroig');
		$row2 = array('id' => 1001, 'name' => 'Jorge Pomer');

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$entities = array($entity1, $entity2);

		$collection = new EntityCollection($entities);

		$expected = array(
			$row1['id'] => (object) $row1,
			$row2['id'] => (object) $row2
		);

		$this->assertEquals($expected, $collection->toObjects());
	}

	/**
	 * sortBy orders entities for integer properties.
	 *
	 * @return  void
	 */
	public function testSortByIntegerOrdering()
	{
		$entity1 = new Entity(1000);
		$entity2 = new Entity(1001);

		$reflection = new \ReflectionClass($entity1);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row1 = array('id' => 1000, 'test_integer' => '4');
		$row2 = array('id' => 1001, 'test_integer' => '3');

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new EntityCollection(array($entity1, $entity2));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortBy('test_integer');

		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortDescendingBy('test_integer');

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$row1 = array('id' => 1000, 'test_integer' => '0');
		$row2 = array('id' => 1001, 'test_integer' => 400);

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new EntityCollection(array($entity1, $entity2));

		$collection->sortBy('test_integer');

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortDescendingBy('test_integer');

		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($collection)));

		$row1 = array('id' => 1000, 'test_integer' => 34);
		$row2 = array('id' => 1001, 'test_integer' => 44);

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new EntityCollection(array($entity1, $entity2));

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortDescendingBy('test_integer');

		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortBy('test_integer');

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * sortBy orders entities for text fields.
	 *
	 * @return  void
	 */
	public function testSortByTextOrdering()
	{
		$entity1 = new Entity(1000);
		$entity2 = new Entity(1001);

		$reflection = new \ReflectionClass($entity1);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row1 = array('id' => 1000, 'test_text' => 'Camióna');
		$row2 = array('id' => 1001, 'test_text' => 'Camión');

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new EntityCollection(array($entity1, $entity2));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortBy('test_text');

		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortDescendingBy('test_text');

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$row1 = array('id' => 1000, 'test_text' => 'Turrón');
		$row2 = array('id' => 1001, 'test_text' => 'tºurrón');

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new EntityCollection(array($entity1, $entity2));

		$collection->sortBy('test_text');

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$collection->sortDescendingBy('test_text');

		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * sort orders entities.
	 *
	 * @return  void
	 */
	public function testSortOrdersEntities()
	{
		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001, 1002), array_keys($entitiesProperty->getValue($collection)));

		$collection->sort(
			function ($entity1, $entity2)
			{
				return ($entity2->getId() < $entity1->getId()) ? -1 : 1;
			}
		);

		$this->assertSame(array(1002, 1001, 1000), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * valid returns correct value.
	 *
	 * @return  void
	 */
	public function testValidReturnsCorrectValue()
	{
		$collection = new EntityCollection;

		$this->assertFalse($collection->valid());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new EntityCollection($entities);

		$this->assertTrue($collection->valid());
	}

	/**
	 * set sets correct value.
	 *
	 * @return  void
	 */
	public function testWriteOverwritesValue()
	{
		$collection = new EntityCollection;

		$this->assertTrue($collection->write(new Entity(1000)));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertEquals(array(1000 => new Entity(1000)), $entitiesProperty->getValue($collection));

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$entity = new Entity(1000);
		$entity2 = new Entity(1000);
		$entity3 = new Entity(1001);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$expectedRow = array('id' => 1000, 'name' => 'Roberto Segura');

		$rowProperty->setValue($entity, $expectedRow);

		$this->assertTrue($collection->write($entity));

		$this->assertSame(array(1000 => $entity), $entitiesProperty->getValue($collection));

		$this->assertTrue($collection->write($entity2));

		$this->assertSame(array(1000 => $entity2), $entitiesProperty->getValue($collection));
	}
}
