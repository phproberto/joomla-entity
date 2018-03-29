<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasComponent;

/**
 * Sample class to test HasComponent trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ContentEntityComponent extends Entity
{
	use HasComponent;
}
