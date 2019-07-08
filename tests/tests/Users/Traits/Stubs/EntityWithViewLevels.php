<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Users\Traits\HasViewLevels;

/**
 * Sample class to test HasViewLevels trait.
 *
 * @since  1.2.0
 *
 * @codeCoverageIgnore
 */
class EntityWithViewLevels extends Entity
{
	use HasViewLevels;

	/**
	 * Expected loadViewLevels result.
	 *
	 * @var  Collection
	 */
	public $loadableViewLevels;

	/**
	 * Load associated view levels.
	 *
	 * @return  Collection
	 */
	protected function loadViewLevels()
	{
		return null === $this->loadableViewLevels ? new Collection : $this->loadableViewLevels;
	}
}
