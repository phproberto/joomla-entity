<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Acl\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Core\Traits\HasState;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Core\Contracts\Publishable;

/**
 * Entity to test Acl decorator.
 *
 * @since  1.1.0
 */
class PublishableEntityWithAcl extends ComponentEntity implements Aclable, Publishable
{
	use HasAcl, HasState;
}
