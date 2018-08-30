<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') or die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Trait for entities with ancestors.
 *
 * @since  1.4.0
 */
trait HasAncestors
{
	/**
	 * Get a specific ancestor by its id.
	 *
	 * @param   integer  $id  Ancestor identifier
	 *
	 * @return  static
	 *
	 * @throws  \InvalidArgumentException  Ancestor not found
	 */
	public function ancestor($id)
	{
		return $this->ancestors()->get($id);
	}

	/**
	 * Get the Ascendants of an entity.
	 *
	 * @return  Collection
	 */
	public function ancestors()
	{
		return $this->searchAncestors();
	}

	/**
	 * Check if this entity has a specific ancestor.
	 *
	 * @param   int  $id  Ascendant identifier
	 *
	 * @return  boolean
	 */
	public function hasAncestor($id)
	{
		return $this->ancestors()->has($id);
	}

	/**
	 * Check if this entity has ancestors.
	 *
	 * @return  boolean
	 */
	public function hasAncestors()
	{
		return !$this->ancestors()->isEmpty();
	}

	/**
	 * Search entity ancestors.
	 *
	 * @param   array  $options  Search options. For filters, limit, ordering, etc.
	 *
	 * @return  Collection
	 */
	abstract public function searchAncestors(array $options = []);
}
