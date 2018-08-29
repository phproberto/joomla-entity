<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories\Traits\Stubs;

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
