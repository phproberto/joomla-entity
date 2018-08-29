<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits;

use Phproberto\Joomla\Entity\Users\Traits\HasAuthor;
use Phproberto\Joomla\Entity\Users\User;

use Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs\EntityWithAuthorAndEditor;

/**
 * HasAuthor trait tests for entities with custom author column.
 *
 * @since   1.1.0
 */
class HasAuthorWithCustomColumnTest extends HasAuthorTest
{
	/**
	 * Name of the author column.
	 *
	 * @const
	 */
	const AUTHOR_COLUMN = 'created_user_id';
}
