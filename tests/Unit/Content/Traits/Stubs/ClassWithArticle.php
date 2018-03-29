<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Content\Traits\Stubs;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Content\Traits\HasArticle;

/**
 * Sample class to test HasArticles trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ClassWithArticle extends Entity
{
	use HasArticle;
}
