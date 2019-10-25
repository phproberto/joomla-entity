<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Table;

defined('_JEXEC') || die;

use Joomla\CMS\Table\Table;
use Phproberto\Joomla\Entity\Extensions\Entity\Component;

/**
 * Table finder
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class TableFinder
{
	/**
	 * Find table in frontend.
	 *
	 * @param   string  $name    Name of the table to load. Example: Article
	 * @param   string  $option  Option of the component where the table is. null = auto-detect. Example: com_content
	 * @param   array   $config  Optional array of configuration for the table
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException  If table not found
	 */
	public static function find($name, $option = null, array $config = [])
	{
		$component = Component::fromOption($option);
		$prefix = $component->prefix() . 'Table';

		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $component->option() . '/tables');

		$table = Table::getInstance($name, $prefix, $config);

		if (!$table instanceof Table)
		{
			throw new \InvalidArgumentException(
				sprintf('Cannot find the table %s in component %s.', $name, $option)
			);
		}

		return $table;
	}
}
