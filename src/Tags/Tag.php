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
	use CoreTraits\HasImages, CoreTraits\HasLink, CoreTraits\HasMetadata, CoreTraits\HasParams;
	use EntityTraits\HasState;

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
	public function table($name = '', $prefix = null, $options = array())
	{
		\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tags/tables');

		$name = $name ?: 'Tag';
		$prefix = $prefix ?: 'TagsTable';

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Load the link to this entity.
	 *
	 * @return  atring
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadLink()
	{
		if (!$this->hasId())
		{
			return null;
		}

		\JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');

		return \JRoute::_(\TagsHelperRoute::getTagRoute($this->id() . '-' . $this->get('alias')));
	}
}
