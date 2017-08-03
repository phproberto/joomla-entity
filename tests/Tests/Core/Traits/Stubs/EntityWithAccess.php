<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasAccess;

/**
 * Sample entity to test HasAccess trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithAccess extends Entity
{
	use HasAccess;
}
