<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\QueryModifier;

defined('_JEXEC') || die;

/**
 * Represents a query modifier.
 *
 * @since  __DEPLOY_VERSION__
 */
interface QueryModifierInterface
{
	/**
	 * Modifies the query.
	 *
	 * @return  void
	 */
	public function apply();
}
