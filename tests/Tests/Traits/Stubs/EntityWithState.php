<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits\HasState;

/**
 * Sample entity to test HasState trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithState extends Entity
{
	use HasState;
}
