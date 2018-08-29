<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasAssociations;

/**
 * Sample entity to test HasAssociations trait.
 *
 * @since  1.1.0
 *
 * @codeCoverageIgnore
 */
class EntityWithAssociations extends Entity
{
	use HasAssociations;

	/**
	 * Expected translations ids for testing.
	 *
	 * @var  array
	 */
	public $associationsIds = array();

	/**
	 * Load associations from DB.
	 *
	 * @return  static[]
	 */
	protected function loadAssociations()
	{
		$associations = array();

		foreach ($this->associationsIds as $langTag => $id)
		{
			$associations[$langTag] = new static($id);
		}

		return $associations;
	}
}
