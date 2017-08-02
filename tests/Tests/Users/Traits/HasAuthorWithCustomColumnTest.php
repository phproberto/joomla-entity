<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits;

use Phproberto\Joomla\Entity\Users\Traits\HasAuthor;
use Phproberto\Joomla\Entity\Users\User;

use Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs\EntityWithAuthorAndEditor;

/**
 * HasAuthor trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAuthorWithCustomColumnTest extends HasAuthorTest
{
	/**
	 * Name of the primary key
	 *
	 * @const
	 */
	const AUTHOR_COLUMN = 'created_user_id';
}
