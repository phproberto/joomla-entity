<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\EntityCollection;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Tags\Traits\HasTags;

/**
 * Sample class to test HasTags trait.
 *
 * @since  __DEPLOY_VERSION__
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
	 * @return  EntityCollection
	 */
	protected function loadTags()
	{
		$collection = new EntityCollection;

		foreach ($this->tagsIds as $tagId)
		{
			$collection->add(new Tag($tagId));
		}

		return $collection;
	}
}
