<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Fields\Traits\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Fields\Traits\HasFields;

/**
 * Entity to test HasFields trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithFields extends ComponentEntity
{
	use HasFields;
}
