<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Categories\Category;

/**
 * Trait for entities that have an asset. Based on category_id|catid column.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasCategory
{
	/**
	 * Associated category.
	 *
	 * @var  Category
	 */
	protected $category;

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function all();

	/**
	 * Get the associated category.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  Category
	 */
	public function category($reload = false)
	{
		if ($reload || null === $this->category)
		{
			$this->category = $this->loadCategory();
		}

		return $this->category;
	}

	/**
	 * Get the name of the column that stores category.
	 *
	 * @return  string
	 */
	protected function getColumnCategory()
	{
		return 'category_id';
	}

	/**
	 * Load the category from the database.
	 *
	 * @return  Category
	 */
	protected function loadCategory()
	{
		$column = $this->getColumnCategory();
		$data = $this->all();

		if (array_key_exists($column, $data))
		{
			return Category::find($data[$column]);
		}

		return new Category;
	}
}
