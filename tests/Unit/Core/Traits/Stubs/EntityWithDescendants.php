<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Core\Traits\HasDescendants;

/**
 * Sample entity to test HasDescendants trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithDescendants extends Entity
{
	use HasDescendants;

	/**
	 * Descendants that will be returned by searchDescendants method.
	 *
	 * @var  Collection
	 */
	public $loadableDescendants;

	/**
	 * Search entity descendants.
	 *
	 * @param   array  $options  Search options. For filters, limit, ordering, etc.
	 *
	 * @return  Collection
	 */
	public function searchDescendants(array $options = [])
	{
		return $this->loadableDescendants ?: new Collection;
	}
}
