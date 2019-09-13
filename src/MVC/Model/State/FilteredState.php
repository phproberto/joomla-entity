<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\StringQuoted;

/**
 * Represents a filtered model state.
 *
 * @since  __DEPLOY_VERSION__
 */
class FilteredState extends State
{
	/**
	 * Get a value from the state.
	 *
	 * @param   string  $key      State property key
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		$value = parent::get($key, $default);

		try
		{
			return $this->property($key)->filter($value);
		}
		catch (\RuntimeException $e)
		{
			$filter = new StringQuoted;

			return $filter->filter($value);
		}
	}
}
