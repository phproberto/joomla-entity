<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Tags\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Tags\Traits\HasTags;

/**
 * Sample class to test HasTags trait.
 *
 * @since  1.1.0
 */
class ClassWithTags extends Entity
{
	use HasTags;

	/**
	 * Expected tags ids for testing.
	 *
	 * @var  array
	 */
	public $tagsIds = array();

	/**
	 * Load associated tags from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTags()
	{
		$collection = new Collection;

		foreach ($this->tagsIds as $tagId)
		{
			$collection->add(new Tag($tagId));
		}

		return $collection;
	}
}
