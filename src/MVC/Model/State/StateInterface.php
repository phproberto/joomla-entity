<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State;

defined('_JEXEC') || die;

/**
 * Methods required by query filterers.
 *
 * @since  __DEPLOY_VERSION__
 */
interface StateInterface
{
	/**
	 * Get a value from the state.
	 *
	 * @param   string  $key      Property key
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function get($key, $default = null);

	/**
	 * Set a value of a state property.
	 *
	 * @param   string  $key    Property key
	 * @param   mixed   $value  Value for the state property
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 */
	public function set($key, $value);
}
