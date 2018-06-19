<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasFeatured;

/**
 * Sample entity to test HasFeatured trait.
 *
 * @since  1.1.0
 */
class EntityWithFeatured extends Entity
{
	use HasFeatured;
}
