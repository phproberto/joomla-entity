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
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasAjaxDelete;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasAssociatedEntity;

/**
 * Class to test HasAjaxDelete trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ControllerWithAjaxDelete extends BaseController
{
	use HasAjaxDelete, HasAssociatedEntity;
}
