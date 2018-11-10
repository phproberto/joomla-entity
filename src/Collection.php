<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Represents a collection of entities.
 *
 * @since   1.0.0
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * Ascending direction for sorting.
	 *
	 * @const
	 */
	const DIRECTION_ASCENDING = 'ASC';

	/**
	 * Descending direction for sorting.
	 *
	 * @const
	 */
	const DIRECTION_DESCENDING = 'DESC';

	/**
	 * Class of the entities in this collection.
	 *
	 * @var  string
	 */
	protected $class;

	/**
	 * @var  array
	 */
	protected $entities = array();

	/**
	 * Constructor.
	 *
	 * @param   EntityInterface[]  $entities  Entities to initialise the collection
	 */
	public function __construct(array $entities = array())
	{
		if ($entities)
		{
			foreach ($entities as $entity)
			{
				$this->add($entity);
			}
		}
	}

	/**
	 * Adds an entity to the collection.
	 * Note: It won't overwrite existing entities.
	 *
	 * @param   EntityInterface  $entity  Entity going to be added
	 *
	 * @return  boolean
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function add(EntityInterface $entity)
	{
		return $this->write($entity, false);
	}

	/**
	 * Get all the entities in the collection as array.
	 *
	 * @return  EntityInterface[]
	 */
	public function all()
	{
		return $this->entities;
	}

	/**
	 * Clears all the entities of the collection.
	 *
	 * @return  self
	 */
	public function clear()
	{
		$this->entities = array();

		return $this;
	}

	/**
	 * Get a clone of the collection entities.
	 *
	 * @return  array
	 */
	private function clonedEntities()
	{
		return array_map(
			function ($entity)
			{
				return clone $entity;
			},
			$this->entities
		);
	}

	/**
	 * Get the count of entities in this collection.
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->entities);
	}

	/**
	 * Get the active entity.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function current()
	{
		return current($this->entities);
	}

	/**
	 * Retrieve a new collection applying a filtering function.
	 *
	 * @param   callable  $function  Filter function
	 *
	 * @return  static
	 *
	 * @since   1.1.0
	 */
	public function filter(callable $function)
	{
		return new static(array_filter($this->clonedEntities(), $function));
	}

	/**
	 * Get the first entity in the collection.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function first()
	{
		return reset($this->entities);
	}

	/**
	 * Generate a collection from an array of entities data.
	 *
	 * @param   array   $data   Array containing entities data. It can be an array of arrays or an array of objects.
	 * @param   string  $class  Class that will be used for entities
	 *
	 * @return  static
	 *
	 * @since   1.4.0
	 */
	public static function fromData(array $data, $class)
	{
		return new static (
			array_map(
				function ($entityData) use ($class)
				{
					$entity = new $class;

					return $entity->bind($entityData);
				},
				$data
			)
		);
	}

	/**
	 * Get an entity by its identifier.
	 *
	 * @param   integer  $id  Item's identifier
	 *
	 * @return  EntityInterface
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function get($id)
	{
		if (!$this->has($id))
		{
			throw new \InvalidArgumentException(sprintf('Error in %s::%s(): Collection does not have %s element', __CLASS__, __FUNCTION__, $id));
		}

		return $this->entities[$id];
	}

	/**
	 * Get an iterator for the entities.
	 *
	 * @return  \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->entities);
	}

	/**
	 * Check if an entity is present in this collection.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return  boolean
	 */
	public function has($id)
	{
		return isset($this->entities[$id]);
	}

	/**
	 * Returns ids of the entities in this collection in the order they were added.
	 *
	 * @return  array
	 */
	public function ids()
	{
		return array_keys($this->entities);
	}

	/**
	 * Get a new collection containing entities present in two collections.
	 *
	 * @param   Collection  $collection  Collection to intersect
	 *
	 * @return  static
	 */
	public function intersect(Collection $collection)
	{
		$intersection = new static;

		if ($collection->isEmpty())
		{
			return $intersection;
		}

		$commonIds = array_intersect(array_keys($this->entities), $collection->ids());

		foreach ($commonIds as $id)
		{
			$intersection->add($this->entities[$id]);
		}

		return $intersection;
	}

	/**
	 * Check if the collection is empty.
	 *
	 * @return  boolean
	 */
	public function isEmpty()
	{
		return !$this->entities;
	}

	/**
	 * Return the id of the active entity.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  integer | null for no entities
	 */
	public function key()
	{
		return key($this->entities);
	}

	/**
	 * Sort collection entities reversely by id.
	 *
	 * @return  static
	 */
	public function krsort()
	{
		$sortedEntities = $this->clonedEntities();

		krsort($sortedEntities);

		return new static($sortedEntities);
	}

	/**
	 * Sort collection entities by id.
	 *
	 * @return  static
	 */
	public function ksort()
	{
		$sortedEntities = $this->clonedEntities();

		ksort($sortedEntities);

		return new static($sortedEntities);
	}

	/**
	 * Get the last entity in the collection.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function last()
	{
		return end($this->entities);
	}

	/**
	 * Execute a function on all the items in the collection.
	 *
	 * @param   callable  $function  Function to execute
	 *
	 * @return  static
	 */
	public function map(callable $function)
	{
		return new static(array_map($function, $this->clonedEntities()));
	}

	/**
	 * Get a new collection containing merged entities from two collections.
	 *
	 * @param   Collection  $collection  Collection to merge
	 *
	 * @return  static
	 */
	public function merge(Collection $collection)
	{
		$merge = clone $this;

		if ($collection->isEmpty())
		{
			return $merge;
		}

		foreach ($collection as $item)
		{
			$merge->add($item);
		}

		return $merge;
	}

	/**
	 * Gets the next entity.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function next()
	{
		return next($this->entities);
	}

	/**
	 * Determine if an entity exists at an offset.
	 * Part of the ArrayAccess implementation.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return  boolean
	 */
	public function offsetExists($id)
	{
		return array_key_exists($id, $this->entities);
	}

	/**
	 * Get entity on the given offset.
	 * Part of the ArrayAccess implementation.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return   EntityInterface
	 */
	public function offsetGet($id)
	{
		return $this->entities[$id];
	}

	/**
	 * Set the entity at a given offset.
	 * Part of the ArrayAccess implementation.
	 *
	 * @param   integer          $id      Entity identifier
	 * @param   EntityInterface  $entity  Entity
	 *
	 * @return  void
	 */
	public function offsetSet($id, $entity)
	{
		$this->write($entity);
	}

	/**
	 * Unset the entity at a given offset.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return void
	 */
	public function offsetUnset($id)
	{
		unset($this->entities[$id]);
	}

	/**
	 * Remove an entity from the collection.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return  boolean
	 */
	public function remove($id)
	{
		if (!$this->has($id))
		{
			return false;
		}

		unset($this->entities[$id]);

		return true;
	}

	/**
	 * Get the first entity in the collection.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function rewind()
	{
		return $this->first();
	}

	/**
	 * Apply custom function to order collection entities.
	 *
	 * @param   callable  $function  Function to sort entities
	 *
	 * @return  static
	 */
	public function sort(callable $function)
	{
		$sortedEntities = $this->clonedEntities();

		uasort($sortedEntities, $function);

		return new static($sortedEntities);
	}

	/**
	 * Sort entities by a property.
	 *
	 * @param   string  $property   Property name
	 * @param   string  $direction  Ordering direction: ASC | DESC
	 *
	 * @return  self
	 */
	public function sortBy($property, $direction = self::DIRECTION_ASCENDING)
	{
		$ascending = $direction === self::DIRECTION_ASCENDING;

		return $this->sort(
			function ($entity1, $entity2) use ($property, $ascending)
			{
				if ($ascending)
				{
					return strcmp($entity1->get($property), $entity2->get($property));
				}

				return strcmp($entity2->get($property), $entity1->get($property));
			}
		);
	}

	/**
	 * Sort entities in descendent order by a property.
	 * This is a fast usage proxy sortBy with descendencing direction.
	 *
	 * @param   string  $property  Property name
	 *
	 * @return  self
	 */
	public function sortByDesc($property)
	{
		return $this->sortBy($property, self::DIRECTION_DESCENDING);
	}

	/**
	 * Convert the collection into an array of arrays.
	 *
	 * @return  array
	 */
	public function toArray()
	{
		$result = array();

		foreach ($this->entities as $id => $entity)
		{
			$result[$id] = $entity->all();
		}

		return $result;
	}

	/**
	 * Get all data from all the entities as objects.
	 *
	 * @return  \stdClass[]  An array of stdClass objects
	 */
	public function toObjects()
	{
		$result = array();

		foreach ($this->entities as $id => $entity)
		{
			$result[$id] = (object) $entity->all();
		}

		return $result;
	}

	/**
	 * Check if there are still entities in the entities array.
	 * Part of the iterator implementation.
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		return key($this->entities) !== null;
	}

	/**
	 * Proxy for add with overwrite enabled.
	 *
	 * @param   EntityInterface  $entity     Entity
	 * @param   boolean          $overwrite  Force writing the entity if it already exists
	 *
	 * @return  boolean
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function write(EntityInterface $entity, $overwrite = true)
	{
		$id = (int) $entity->id();

		if (!$id)
		{
			throw new \InvalidArgumentException("Cannot add entity without id to the collection");
		}

		if (empty($this->class))
		{
			$this->class = get_class($entity);
		}
		elseif ($this->class !== get_class($entity))
		{
			throw new \InvalidArgumentException("Trying to add a `%s` entity to a collection of `%s`");
		}

		if (!$overwrite && $this->has($id))
		{
			return false;
		}

		$this->entities[$id] = $entity;

		return true;
	}
}
