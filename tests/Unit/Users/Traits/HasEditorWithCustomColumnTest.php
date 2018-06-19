<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits;

use Phproberto\Joomla\Entity\Users\Traits\HasAuthor;
use Phproberto\Joomla\Entity\Users\User;

use Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs\EntityWithAuthorAndEditor;

/**
 * HasEditor trait tests for entities with custom editor column.
 *
 * @since   1.1.0
 */
class HasEditorWithCustomColumnTest extends HasEditorTest
{
	/**
	 * Name of the editor column.
	 *
	 * @const
	 */
	const EDITOR_COLUMN = 'modified_user_id';
}
