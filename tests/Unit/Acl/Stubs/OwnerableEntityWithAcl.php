<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Acl\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Users\Traits\HasOwner;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;

/**
 * Entity to test Acl decorator.
 *
 * @since  1.1.0
 */
class OwnerableEntityWithAcl extends ComponentEntity implements Aclable, Ownerable
{
	use HasAcl, HasOwner;
}
