<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') or die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Trait for entities with child entities.
 *
 * @since  1.4.0
 */
trait HasChildren
{
	/**
	 * Get a specific child by its id.
	 *
	 * @param   int  $id  Ascendant identifier
	 *
	 * @return  mixed  null || static
	 *
	 * @throws  \InvalidArgumentException  Descendant not found
	 */
	public function child($id)
	{
		return $this->children()->get($id);
	}

	/**
	 * Get the children of this entity.
	 *
	 * @return  Collection
	 */
	public function children()
	{
		return $this->searchChildren();
	}

	/**
	 * Check if this entity has an specific child.
	 *
	 * @param   int  $id  Ascendant identifier
	 *
	 * @return  boolean
	 */
	public function hasChild($id)
	{
		return $this->children()->has($id);
	}

	/**
	 * Check if this entity has children.
	 *
	 * @return  boolean
	 */
	public function hasChildren()
	{
		return !$this->children()->isEmpty();
	}

	/**
	 * Search entity children.
	 *
	 * @param   array  $options  Search options. For filters, limit, ordering, etc.
	 *
	 * @return  Collection
	 */
	abstract public function searchChildren(array $options = []);
}
