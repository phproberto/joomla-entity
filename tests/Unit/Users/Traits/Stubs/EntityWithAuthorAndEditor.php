<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Users\Traits\HasAuthor;
use Phproberto\Joomla\Entity\Users\Traits\HasEditor;

/**
 * Sample class to test HasAuthor & HasEditor traits.
 *
 * @since  __DEPLOY_VERSION__
 *
 * @codeCoverageIgnore
 */
class EntityWithAuthorAndEditor extends Entity
{
	use HasAuthor, HasEditor;
}
