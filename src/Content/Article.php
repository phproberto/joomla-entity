<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Categories\Traits as CategoriesTraits;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Traits as EntityTraits;

/**
 * Stub to test Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
class Article extends Entity
{
	use CategoriesTraits\HasCategory, CoreTraits\HasAsset, EntityTraits\HasParams;

	/**
	 * Get the name of the column that stores params.
	 *
	 * @return  string
	 */
	protected function getParamsColumn()
	{
		return 'attribs';
	}

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
		$name = $name ?: 'Content';
		$prefix = $prefix ?: 'JTable';

		return parent::getTable($name, $prefix, $options);
	}
}
