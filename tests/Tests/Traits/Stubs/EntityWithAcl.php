<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits\HasAcl;

/**
 * Sample entity to test HasAcl trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithAcl extends Entity
{
	use HasAcl;
}
