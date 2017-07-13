<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

use Phproberto\Joomla\Entity\EntityInterface;

/**
 * Represents a collection of entities.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityCollection implements \Countable, \Iterator
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
	 * @param   EntityCollection  $collection  Collection to intersect
	 *
	 * @return  static
	 */
	public function intersect(EntityCollection $collection)
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
	 * @return  self
	 */
	public function krsort()
	{
		krsort($this->entities);

		return $this;
	}

	/**
	 * Sort collection entities by id.
	 *
	 * @return  self
	 */
	public function ksort()
	{
		ksort($this->entities);

		return $this;
	}

	/**
	 * Get a new collection containing merged entities from two collections.
	 *
	 * @param   EntityCollection  $collection  Collection to merge
	 *
	 * @return  static
	 */
	public function merge(EntityCollection $collection)
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
		return reset($this->entities);
	}

	/**
	 * Apply custom function to order collection entities.
	 *
	 * @param   callable  $func  Function to sort entities
	 *
	 * @return  self
	 */
	public function sort(callable $func)
	{
		uasort($this->entities, $func);

		return $this;
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

		$this->sort(
			function ($entity1, $entity2) use ($property, $ascending)
			{
				if ($ascending)
				{
					return strcmp($entity1->get($property), $entity2->get($property));
				}

				return strcmp($entity2->get($property), $entity1->get($property));
			}
		);

		return $this;
	}

	/**
	 * Sort entities in descendent order by a property.
	 * This is a fast usage proxy sortBy with descendencing direction.
	 *
	 * @param   string  $property  Property name
	 *
	 * @return  self
	 */
	public function sortDescendingBy($property)
	{
		return $this->sortBy($property, self::DIRECTION_DESCENDING);
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
			$result[$id] = (object) $entity->getAll();
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

		if (!$overwrite && $this->has($id))
		{
			return false;
		}

		$this->entities[$id] = $entity;

		return true;
	}
}
