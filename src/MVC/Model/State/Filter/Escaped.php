<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;

/**
 * Strings escaped by database driver.
 *
 * @since  __DEPLOY_VERSION__
 */
class Escaped extends StringFilter
{
	/**
	 * Prepare value.
	 *
	 * @param   mixed  $value  Value to prepare
	 *
	 * @return  mixed
	 */
	public function prepareValue($value)
	{
		$value = parent::prepareValue($value);

		if (!is_string($value))
		{
			return null;
		}

		return $this->getDbo()->escape($value);
	}

	/**
	 * Isolated factory communication to ease testing.
	 *
	 * @return  \JDatabaseDriver
	 */
	protected function getDbo()
	{
		return Factory::getDbo();
	}
}
