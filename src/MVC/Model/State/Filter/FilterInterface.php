<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State\Filter;

defined('_JEXEC') || die;

/**
 * Methods required by query filterers.
 *
 * @since  __DEPLOY_VERSION__
 */
interface FilterInterface
{
	/**
	 * Filter one or more values received from the state.
	 *
	 * @param   mixed  $values  Values to filter
	 *
	 * @return  array
	 */
	public function filter($values);
}
