<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Core\Traits\HasLinks;

/**
 * Sample entity to test HasLinks trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithLinks extends Entity
{
	use HasLinks;

	/**
	 * Get the list of column aliases.
	 *
	 * @return  array
	 */
	public function columnAliases()
	{
		return [
			Column::ALIAS => 'alias'
		];
	}

	/**
	 * Component option.
	 *
	 * @return  mixed  null (not found) | string (found)
	 */
	protected function componentOption()
	{
		return 'com_entity_with_links';
	}
}
