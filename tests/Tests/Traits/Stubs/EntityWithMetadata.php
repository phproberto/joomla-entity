<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits\HasMetadata;

/**
 * Sample entity to test HasMetadata trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithMetadata extends Entity
{
	use HasMetadata;
}
