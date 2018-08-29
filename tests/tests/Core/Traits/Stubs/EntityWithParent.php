<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasParent;

/**
 * Sample entity to test HasParent trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithParent extends Entity
{
	use HasParent;
}
