<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model;

defined('_JEXEC') || die;

use Phproberto\Joomla\Model\State\StateInterface;

/**
 * Represents a model with plugable state classes.
 *
 * @since  __DEPLOY_VERSION__
 */
interface ModelWithStateInterface
{
	/**
	 * Retrieve the model state.
	 *
	 * @return  StateInterface
	 */
	public function state();
}
