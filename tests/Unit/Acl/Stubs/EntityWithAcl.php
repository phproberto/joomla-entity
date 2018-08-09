<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Acl\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;

/**
 * Entity to test Acl decorator.
 *
 * @since  1.1.0
 */
class EntityWithAcl extends ComponentEntity implements Aclable
{
	use HasAcl;
}
