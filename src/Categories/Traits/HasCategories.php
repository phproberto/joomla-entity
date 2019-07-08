<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Categories\Category;

/**
 * Trait for entities that have associated categories.
 *
 * @since  1.0.0
 */
trait HasCategories
{
	/**
	 * Associated categories.
	 *
	 * @var  Collection
	 */
	protected $categories;

	/**
	 * Get the associated categories.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  Collection
	 */
	public function categories($reload = false)
	{
		if ($reload || null === $this->categories)
		{
			$this->categories = $this->loadCategories();
		}

		return $this->categories;
	}

	/**
	 * Clear already loaded categories.
	 *
	 * @return  self
	 */
	public function clearCategories()
	{
		$this->categories = null;

		return $this;
	}

	/**
	 * Check if this entity has an associated category.
	 *
	 * @param   integer   $id  Category identifier
	 *
	 * @return  boolean
	 */
	public function hasCategory($id)
	{
		return $this->categories()->has($id);
	}

	/**
	 * Check if this entity has associated categories.
	 *
	 * @return  boolean
	 */
	public function hasCategories()
	{
		return !$this->categories()->isEmpty();
	}

	/**
	 * Load associated categories from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadCategories();
}
