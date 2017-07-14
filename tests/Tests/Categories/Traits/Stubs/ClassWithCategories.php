<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Categories\Traits\HasCategories;

/**
 * Sample class to test HasCategories trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ClassWithCategories extends Entity
{
	use HasCategories;

	/**
	 * Expected categories ids for testing.
	 *
	 * @var  array
	 */
	public $categoriesIds = array();

	/**
	 * Load associated categories from DB.
	 *
	 * @return  Collection
	 */
	protected function loadCategories()
	{
		$collection = new Collection;

		foreach ($this->categoriesIds as $id)
		{
			$collection->add(new Category($id));
		}

		return $collection;
	}
}
