<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Contracts\ComponentEntityInterface;

/**
 * Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class ComponentEntity extends Entity implements ComponentEntityInterface
{
	use CoreTraits\HasComponent;
}
