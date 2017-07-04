<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits\HasLink;

/**
 * Sample entity to test HasLink trait.
 *
 * @since  __DEPLOY_VERSION__
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
		$slug = $this->getSlug();

		if (!$slug)
		{
			return null;
		}

		return \JUri::root(true) . '/' . $slug;
	}
}
