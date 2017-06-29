<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits\HasParams;

/**
 * Sample entity to test HasParams trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithParams extends Entity
{
	use HasParams;
}
