<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasMetadata;

/**
 * Sample entity to test HasMetadata trait.
 *
 * @since  1.1.0
 */
class EntityWithMetadata extends Entity
{
	use HasMetadata;
}
