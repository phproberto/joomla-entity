<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Contracts\ComponentEntityInterface;

/**
 * Entity class.
 *
 * @since   1.0.0
 */
abstract class ComponentEntity extends Entity implements ComponentEntityInterface
{
	use CoreTraits\HasComponent;
}
