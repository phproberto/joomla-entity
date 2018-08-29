<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Content\Traits\HasArticles;

/**
 * Sample class to test HasArticles trait.
 *
 * @since  1.1.0
 */
class ClassWithArticles extends Entity
{
	use HasArticles;

	/**
	 * Expected articles ids for testing.
	 *
	 * @var  array
	 */
	public $articlesIds = array();

	/**
	 * Load associated articles from DB.
	 *
	 * @return  Collection
	 */
	protected function loadArticles()
	{
		$collection = new Collection;

		foreach ($this->articlesIds as $id)
		{
			$collection->add(new Article($id));
		}

		return $collection;
	}
}
