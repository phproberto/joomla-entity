<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tags;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Categories\Traits as CategoriesTraits;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Traits as EntityTraits;

/**
 * Tag entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Tag extends Entity
{
	use EntityTraits\HasImages, EntityTraits\HasMetadata, EntityTraits\HasParams, EntityTraits\HasState;

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
		\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tags/tables');

		$name = $name ?: 'Tag';
		$prefix = $prefix ?: 'TagsTable';

		return parent::getTable($name, $prefix, $options);
	}
}
