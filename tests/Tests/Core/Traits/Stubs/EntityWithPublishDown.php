<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits\HasPublishDown;

/**
 * Sample entity to test HasPublishDown trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithPublishDown extends Entity
{
	use HasPublishDown;
}
