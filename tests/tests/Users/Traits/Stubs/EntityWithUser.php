<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Users\Traits\HasUser;

/**
 * Sample class to test HasUser traits.
 *
 * @since  1.1.0
 *
 * @codeCoverageIgnore
 */
class EntityWithUser extends Entity
{
	use HasUser;
}
