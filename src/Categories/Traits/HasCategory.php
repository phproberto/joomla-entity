<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Categories\Traits;

use Phproberto\Joomla\Entity\Categories\Category;

defined('JPATH_PLATFORM') || die;

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
	 * Get the associated category.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  Category
	 */
	public function getCategory($reload = false)
	{
		if ($reload || null === $this->category)
		{
			$this->category = $this->loadCategory();
		}

		return $this->category;
	}

	/**
	 * Load the category from the database.
	 *
	 * @return  Category
	 */
	protected function loadCategory()
	{
		$row = $this->getRow();

		if (!empty($row['category_id']))
		{
			return Category::instance($row['category_id']);
		}

		if (!empty($row['catid']))
		{
			return Category::instance($row['catid']);
		}

		return new Category;
	}
}
