<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Core\Traits\HasChildren;

/**
 * Sample entity to test HasChildren trait.
 *
 * @since  1.4.0
 */
class EntityWithChildren extends Entity
{
	use HasChildren;

	/**
	 * Children that will be returned by searchChildren method.
	 *
	 * @var  Collection
	 */
	public $loadableChildren;

	/**
	 * Search entity children.
	 *
	 * @param   array  $options  Search options. For filters, limit, ordering, etc.
	 *
	 * @return  Collection
	 */
	public function searchChildren(array $options = [])
	{
		return $this->loadableChildren ?: new Collection;
	}
}
