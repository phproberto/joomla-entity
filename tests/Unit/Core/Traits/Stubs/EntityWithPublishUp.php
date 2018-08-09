<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasPublishUp;

/**
 * Sample entity to test HasPublishUp trait.
 *
 * @since  1.1.0
 */
class EntityWithPublishUp extends Entity
{
	use HasPublishUp;
}
