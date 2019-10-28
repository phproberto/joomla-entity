<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Controller\BaseController;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasEntityCreate;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasEntityDelete;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasEntityUpdate;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasAssociatedEntity;

/**
 * Class to test HasEntitySave trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ControllerWithEntityCRUD extends BaseController
{
	use HasEntityCreate, HasEntityDelete, HasEntityUpdate, HasAssociatedEntity;
}
