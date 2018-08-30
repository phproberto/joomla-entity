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
use Phproberto\Joomla\Entity\Core\Traits\HasAncestors;

/**
 * Sample entity to test HasAncestors trait.
 *
 * @since  1.4.0
 */
class EntityWithAncestors extends Entity
{
	use HasAncestors;

	/**
	 * Ancestors that will be returned by searchAncestors method.
	 *
	 * @var  Collection
	 */
	public $loadableAncestors;

	/**
	 * Search entity ancestors.
	 *
	 * @param   array  $options  Search options. For filters, limit, ordering, etc.
	 *
	 * @return  Collection
	 */
	public function searchAncestors(array $options = [])
	{
		return $this->loadableAncestors ?: new Collection;
	}
}
