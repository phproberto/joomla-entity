<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Joomla\CMS\Uri\Uri;
use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasLink;

/**
 * Sample entity to test HasLink trait.
 *
 * @since  1.1.0
 */
class EntityWithLink extends Entity
{
	use HasLink;

	/**
	 * Load the link to this entity.
	 *
	 * @return  atring
	 */
	protected function loadLink()
	{
		$slug = $this->slug();

		if (!$slug)
		{
			return null;
		}

		return Uri::root(true) . '/' . $slug;
	}
}
