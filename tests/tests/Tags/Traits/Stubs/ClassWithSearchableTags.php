<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Tags\Traits\HasTags;

/**
 * Sample class to test HasTags trait.
 *
 * @since  1.7.0
 */
class ClassWithSearchableTags extends Entity
{
	use HasTags;

	/**
	 * Retrieve the alias of content type associated with this entity.
	 *
	 * @return  string
	 *
	 * @since   1.7.0
	 */
	public static function contentTypeAlias()
	{
		return 'com_content.category';
	}
}
