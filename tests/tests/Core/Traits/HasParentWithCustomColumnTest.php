<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

defined('_JEXEC') || die;

/**
 * HasParent with custom column tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasParentWithCustomColumnTest extends HasParentTest
{
	/**
	 * Name of the column used to store parent identifier.
	 *
	 * @const
	 */
	const PARENT_COLUMN = 'custom_parent_id';
}
