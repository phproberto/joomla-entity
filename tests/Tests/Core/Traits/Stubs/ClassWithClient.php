<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasClient;

/**
 * Sample class to test HasArticles trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ClassWithClient extends Entity
{
	use HasClient;
}
