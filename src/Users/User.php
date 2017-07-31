<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Traits as EntityTraits;

/**
 * User entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class User extends Entity
{
	use EntityTraits\HasParams;
}
