<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Traits\HasSingleton;

/**
 * HasSingleton tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ClassWithSingleton
{
	use HasSingleton;
}
