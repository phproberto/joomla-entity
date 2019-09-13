<?php
/**
 * @package     Phproberto\Joomla\Model
 * @subpackage  Tests.Unit
 *
 * @copyright   Copyright (C) 2018 Roberto Segura López. All rights reserved.
 * @license     GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Phproberto\Joomla\Model\Tests\Unit\State\Stubs;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\ListModel;
use Phproberto\Joomla\Entity\MVC\Model\ModelWithStateInterface;

/**
 * Model to test state integration.
 *
 * @since  __DEPLOY_VERSION__
 */
class ModelWithState extends ListModel implements ModelWithStateInterface
{
}
