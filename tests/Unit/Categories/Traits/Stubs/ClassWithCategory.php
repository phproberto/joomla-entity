<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Categories\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Categories\Traits\HasCategory;

/**
 * Sample class to test HasCategory trait.
 *
 * @since  1.1.0
 */
class ClassWithCategory extends Entity
{
	use HasCategory;
}
