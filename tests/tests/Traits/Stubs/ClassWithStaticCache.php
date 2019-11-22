<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits\Stubs;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Traits\HasStaticCache;

/**
 * HasAssociatedEntity trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ClassWithStaticCache
{
	use HasStaticCache;
}
