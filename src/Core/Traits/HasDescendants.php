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
 * Trait for entities with descendants.
 *
 * @since  1.4.0
 */
trait HasDescendants
{
	/**
	 * Get a specific descendant by its id.
	 *
	 * @param   int  $id  Ascendant identifier
	 *
	 * @return  mixed  null || static
	 *
	 * @throws  \InvalidArgumentException  Descendant not found
	 */
	public function descendant($id)
	{
		return $this->descendants()->get($id);
	}

	/**
	 * Get the descendants of this entity.
	 *
	 * @return  Collection
	 */
	public function descendants()
	{
		return $this->searchDescendants();
	}

	/**
	 * Check if this entity has an specific descendant.
	 *
	 * @param   int  $id  Ascendant identifier
	 *
	 * @return  boolean
	 */
	public function hasDescendant($id)
	{
		return $this->descendants()->has($id);
	}

	/**
	 * Check if this entity has descendants.
	 *
	 * @return  boolean
	 */
	public function hasDescendants()
	{
		return !$this->descendants()->isEmpty();
	}

	/**
	 * Search entity descendants.
	 *
	 * @param   array  $options  Search options. For filters, limit, ordering, etc.
	 *
	 * @return  Collection
	 */
	abstract public function searchDescendants(array $options = []);
}
