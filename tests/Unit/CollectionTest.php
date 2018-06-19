<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests;

use Phproberto\Joomla\Entity\Tests\Unit\Stubs\AnotherEntity;
use Phproberto\Joomla\Entity\Tests\Unit\Stubs\Entity;
use Phproberto\Joomla\Entity\Collection;

/**
 * Entity collection tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CollectionTest extends \TestCase
{
	/**
	 * Constructor sets entities.
	 *
	 * @return  void
	 */
	public function testConstructorSetsEntities()
	{
		$collection = new Collection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(), $entitiesProperty->getValue($collection));

		$entities = array(
			new Entity(1000),
			new Entity(1001)
		);

		$collection = new Collection($entities);

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
		$collection = new Collection;

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
	 * add does not overwrite entity.
	 *
	 * @return  void
	 */
	public function testAddDoesNotOverwriteEntity()
	{
		$collection = new Collection;

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
	 * arrayAccess implementation.
	 *
	 * @return  void
	 */
	public function testArrayAccessImplementation()
	{
		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		$this->assertTrue(isset($collection[1000]));
		$this->assertFalse(isset($collection[1003]));

		$collection[1003] = new Entity(1003);

		$this->assertTrue(isset($collection[1003]));
		$this->assertEquals(new Entity(1002), $collection[1002]);

		unset($collection[1003]);
		$this->assertFalse(isset($collection[1003]));
	}

	/**
	 * clear empties entities array.
	 *
	 * @return  void
	 */
	public function testClearEmptiesEntitiesArray()
	{
		$collection = new Collection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

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
		$collection = new Collection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(0, $collection->count());

		$collection = new Collection(array(new Entity(1000), new Entity(1001)));

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
		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		while ($collection->key())
		{
			$this->assertEquals(current($entities), $collection->current());
			next($entities);
			$collection->next();
		}

		reset($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$collection = new Collection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

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
	 * all returns correct value.
	 *
	 * @return  void
	 */
	public function testAllReturnsCorrectValue()
	{
		$collection = new Collection;

		$this->assertSame(array(), $collection->all());

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001)
		);

		$collection = new Collection($entities);

		$returnedEntities = $collection->all();

		$this->assertSame($entities, $returnedEntities);

		// Test that writing does not modify source entities
		$returnedEntities[1000]->publicProperty = 'test me';

		$this->assertSame($entities, $returnedEntities);
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function fitlerReturnsANewCollection()
	{
		$collection = new Collection;

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001)
		);

		$collection = new Collection($entities);

		$this->assertSame(2, $collection->count());

		$newCollection = $collection->filter(
			function ($entity)
			{
				return $entity->id() === 1000;
			}
		);

		$this->assertNotSame($newCollection, $collection);
		$this->assertSame(1, $newCollection->count());
		$this->assertSame(1000, $newCollection[1000]->id());
	}

	/**
	 * getIterator returns correct iterator.
	 *
	 * @return  void
	 */
	public function testGetIteratorReturnsCorrectIterator()
	{
		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		$this->assertEquals(new \ArrayIterator($entities), $collection->getIterator());
	}

	/**
	 * get retrieves correct entity.
	 *
	 * @return  void
	 */
	public function testGetRetrievesCorrectEntity()
	{
		$collection = new Collection(array(new Entity(1000), new Entity(1001)));

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
		$collection = new Collection(array(new Entity(1000), new Entity(1001)));

		$collection->get(1002);
	}

	/**
	 * has returns correct vlaue.
	 *
	 * @return  void
	 */
	public function testHasReturnsCorrectValue()
	{
		$collection = new Collection;

		$this->assertFalse($collection->has(1000));
		$this->assertFalse($collection->has(1001));
		$this->assertFalse($collection->has(1002));

		$collection = new Collection(array(new Entity(1000), new Entity(1001)));

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
		$collection = new Collection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(), $collection->ids());

		$collection = new Collection(array(new Entity(1000), new Entity(1001)));

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
		$collection1 = new Collection;
		$collection2 = new Collection;

		$result = $collection1->intersect($collection2);

		$reflection = new \ReflectionClass($result);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertEquals(array(), $entitiesProperty->getValue($result));

		$collection1 = new Collection(array(new Entity(1000), new Entity(1001)));
		$collection2 = new Collection(array(new Entity(1002), new Entity(1000)));

		$result = $collection1->intersect($collection2);

		$expectetdEntities = array(
			1000 => new Entity(1000)
		);

		$this->assertEquals($expectetdEntities, $entitiesProperty->getValue($result));
		$this->assertSame(array(1000), array_keys($entitiesProperty->getValue($result)));

		// Ensure that source entities aren't modified
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection1)));
		$this->assertSame(array(1002, 1000), array_keys($entitiesProperty->getValue($collection2)));

		$collection1 = new Collection(array(new Entity(999), new Entity(1000), new Entity(1001)));
		$collection2 = new Collection(array(new Entity(1001), new Entity(1000), new Entity(1002),));

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
		$collection = new Collection;

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertTrue($collection->isEmpty());

		$collection = new Collection(array(new Entity(1000), new Entity(1001)));

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
		$collection = new Collection;

		$this->assertSame(null, $collection->key());

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		while ($collection->key())
		{
			$this->assertEquals(key($entities), $collection->key());
			next($entities);
			$collection->next();
		}

		reset($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$collection = new Collection($entities);

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

		$collection = new Collection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->ksort();

		$this->assertSame(array(1000, 1001, 1002), array_keys($entitiesProperty->getValue($newCollection)));

		// Ensure source collection integrity
		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * krsort orders entities.
	 *
	 * @return  void
	 */
	public function testKrsortOrdersEntities()
	{
		$entities = array(1001 => new Entity(1001), 1000 => new Entity(1000), 1002 => new Entity(1002));

		$collection = new Collection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->krsort();

		$this->assertSame(array(1002, 1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));

		// Ensure source collection integrity
		$this->assertSame(array(1001, 1000, 1002), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * last returns correct value.
	 *
	 * @return  void
	 */
	public function testLastReturnsCorrectValue()
	{
		$collection = new Collection;

		$this->assertSame(false, $collection->last());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new Collection($entities);

		$this->assertSame($entities[1002], $collection->last());

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		while (key($entities) !== 1001)
		{
			next($entities);
		}

		$entitiesProperty->setValue($collection, $entities);

		$this->assertSame(1001, key($entitiesProperty->getValue($collection)));
		$this->assertEquals(new Entity(1002), $collection->last());
	}

	/**
	 * map processes all the entities.
	 *
	 * @return  void
	 */
	public function testMapProcessesAllTheEntities()
	{
		$entity1 = new Entity(1000);
		$entity2 = new Entity(1001);

		$reflection = new \ReflectionClass($entity1);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$row1 = array('id' => 1000, 'name' => 'Vicente Monroig', 'foo' => 'foo');
		$row2 = array('id' => 1001, 'name' => 'Jorge Pomer', 'foo' => 'foo');

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$entities = array($entity1, $entity2);

		$collection = new Collection($entities);

		$function = function ($entity)
		{
			$this->assertSame(null, $entity->publicProperty);
			$entity->assign('foo', 'bar');

			return $entity;
		};

		$newCollection = $collection->map($function);

		foreach ($newCollection as $entity)
		{
			$this->assertSame('bar', $entity->get('foo'));
		}

		$this->assertNotEquals($newCollection, $collection);

		// Original collection not modified
		foreach ($collection as $entity)
		{
			$this->assertSame('foo', $entity->get('foo'));
		}
	}

	/**
	 * Merge returns correct collection.
	 *
	 * @return  void
	 */
	public function testMergeReturnsCorrectCollection()
	{
		$collection1 = new Collection;
		$collection2 = new Collection;

		$mergedCollection = $collection1->merge($collection2);

		$reflection = new \ReflectionClass($mergedCollection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertEquals(array(), $entitiesProperty->getValue($mergedCollection));

		$collection1 = new Collection(array(new Entity(1000), new Entity(1001)));
		$collection2 = new Collection(array(new Entity(1002), new Entity(1003)));

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

		$collection1 = new Collection(array(new Entity(1000), new Entity(1001)));
		$collection2 = new Collection(array(new Entity(1002), new Entity(1003)));

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
		$collection = new Collection;

		$this->assertSame(false, $collection->next());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new Collection($entities);

		foreach ($collection as $entity)
		{
			if ($entity->id() !== 1002)
			{
				$this->assertSame($collection->next(), $entities[$entity->id() + 1]);
			}
		}

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$collection = new Collection(array(new Entity(1000), new Entity(1001), new Entity(1002)));

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
	 * offsetExists returns correct value.
	 *
	 * @return  void
	 */
	public function testOffsetExistsReturnsCorrectValue()
	{
		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		$this->assertFalse($collection->offsetExists(1003));
		$this->assertTrue($collection->offsetExists(1000));
		$this->assertFalse($collection->offsetExists(999));
		$this->assertTrue($collection->offsetExists(1001));
		$this->assertFalse($collection->offsetExists(1004));
		$this->assertTrue($collection->offsetExists(1002));
	}

	/**
	 * offsetGet returns correct value.
	 *
	 * @return  void
	 */
	public function testOffsetGetReturnsCorrectValue()
	{
		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		$this->assertInstanceOf(Entity::class, $collection->offsetGet(1000));
		$this->assertInstanceOf(Entity::class, $collection->offsetGet(1002));
		$this->assertInstanceOf(Entity::class, $collection->offsetGet(1001));
	}

	/**
	 * offsetSet sets correct value.
	 *
	 * @return  void
	 */
	public function testOffsetSetSetsCorrectValue()
	{
		$collection = new Collection;

		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		foreach ($entities as $id => $entity)
		{
			$collection->offsetSet($id, $entity);
			$this->assertSame($entity, $collection[$id]);
		}
	}

	/**
	 * ofssetUnset unsets entity.
	 *
	 * @return  void
	 */
	public function testOffsetUnsetUnsetsEntity()
	{
		$entities = array(
			1000 => new Entity(1000),
			1001 => new Entity(1001),
			1002 => new Entity(1002)
		);

		$collection = new Collection($entities);

		$collection->offsetUnset(1001);
		$this->assertEquals($collection, new Collection(array($entities[1000], $entities[1002])));

		$collection->offsetUnset(1002);
		$this->assertEquals($collection, new Collection(array($entities[1000])));
	}

	/**
	 * remove removes entity.
	 *
	 * @return  void
	 */
	public function testRemoveRemovesEntity()
	{
		$collection = new Collection;

		$this->assertSame(false, $collection->remove(1000));

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new Collection($entities);

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
		$collection = new Collection;

		$this->assertSame(false, $collection->rewind());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new Collection($entities);

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
	 * toArray returns correct data.
	 *
	 * @return  void
	 */
	public function testToArrayReturnsCorrectData()
	{
		$collection = new Collection;

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

		$collection = new Collection($entities);

		$expected = array(
			$row1['id'] => $row1,
			$row2['id'] => $row2
		);

		$this->assertEquals($expected, $collection->toArray());
	}

	/**
	 * toObjects returns correct data.
	 *
	 * @return  void
	 */
	public function testToObjectsReturnsCorrectData()
	{
		$collection = new Collection;

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

		$collection = new Collection($entities);

		$expected = array(
			$row1['id'] => (object) $row1,
			$row2['id'] => (object) $row2
		);

		$this->assertEquals($expected, $collection->toObjects());
	}

	/**
	 * sortyBy does not modify source collection.
	 *
	 * @return  void
	 */
	public function testSortByDoesNotModifySourceCollection()
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

		$collection = new Collection(array($entity1, $entity2));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->sortBy('test_integer');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->sortByDesc('test_integer');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));
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

		$collection = new Collection(array($entity1, $entity2));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->sortBy('test_integer');
		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));

		$newCollection = $collection->sortByDesc('test_integer');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($newCollection)));

		$row1 = array('id' => 1000, 'test_integer' => '0');
		$row2 = array('id' => 1001, 'test_integer' => 400);

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new Collection(array($entity1, $entity2));

		$newCollection = $collection->sortBy('test_integer');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($newCollection)));

		$newCollection = $collection->sortByDesc('test_integer');
		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));

		$row1 = array('id' => 1000, 'test_integer' => 34);
		$row2 = array('id' => 1001, 'test_integer' => 44);

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new Collection(array($entity1, $entity2));

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->sortByDesc('test_integer');
		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));

		$newCollection = $collection->sortBy('test_integer');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($newCollection)));
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

		$collection = new Collection(array($entity1, $entity2));

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->sortBy('test_text');
		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));

		$newCollection = $collection->sortByDesc('test_text');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($newCollection)));

		$row1 = array('id' => 1000, 'test_text' => 'Turrón');
		$row2 = array('id' => 1001, 'test_text' => 'tºurrón');

		$rowProperty->setValue($entity1, $row1);
		$rowProperty->setValue($entity2, $row2);

		$collection = new Collection(array($entity1, $entity2));

		$newCollection = $collection->sortBy('test_text');
		$this->assertSame(array(1000, 1001), array_keys($entitiesProperty->getValue($newCollection)));

		$newCollection = $collection->sortByDesc('test_text');
		$this->assertSame(array(1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));
	}

	/**
	 * sort orders entities.
	 *
	 * @return  void
	 */
	public function testSortOrdersEntities()
	{
		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new Collection($entities);

		$reflection = new \ReflectionClass($collection);
		$entitiesProperty = $reflection->getProperty('entities');
		$entitiesProperty->setAccessible(true);

		$this->assertSame(array(1000, 1001, 1002), array_keys($entitiesProperty->getValue($collection)));

		$newCollection = $collection->sort(
			function ($entity1, $entity2)
			{
				return ($entity2->id() < $entity1->id()) ? -1 : 1;
			}
		);

		$this->assertSame(array(1002, 1001, 1000), array_keys($entitiesProperty->getValue($newCollection)));

		// Ensure integrity of source collection
		$this->assertSame(array(1000, 1001, 1002), array_keys($entitiesProperty->getValue($collection)));
	}

	/**
	 * valid returns correct value.
	 *
	 * @return  void
	 */
	public function testValidReturnsCorrectValue()
	{
		$collection = new Collection;

		$this->assertFalse($collection->valid());

		$entities = array(1000 => new Entity(1000), 1001 => new Entity(1001), 1002 => new Entity(1002));

		$collection = new Collection($entities);

		$this->assertTrue($collection->valid());
	}

	/**
	 * set sets correct value.
	 *
	 * @return  void
	 */
	public function testWriteOverwritesValue()
	{
		$collection = new Collection;

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

	/**
	 * write throws exception when entity has no id.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 *
	 */
	public function testWriteThrowsExceptionWhenEntityHasNoId()
	{
		$collection = new Collection;

		$collection->add(new Entity);
	}

	/**
	 * write throws exception when entity has a different class of the collection.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 *
	 */
	public function testWriteThrowsExceptionWhenEntityHasWrongClass()
	{
		$collection = new Collection;

		$collection->add(new Entity(24));
		$collection->add(new AnotherEntity(23));
	}
}
