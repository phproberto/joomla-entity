<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;

/**
 * Stub to test Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
class Category extends Entity
{
	use CoreTraits\HasAsset;

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	public function getTable($name = '', $prefix = null, $options = array())
	{
		\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');

		$name = $name ?: 'Category';
		$prefix = $prefix ?: 'CategoriesTable';

		return parent::getTable($name, $prefix, $options);
	}
}
