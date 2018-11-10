<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tags;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Core\Contracts\Publishable;

/**
 * Tag entity.
 *
 * @since   1.0.0
 */
class Tag extends ComponentEntity implements Aclable, Publishable
{
	use HasAcl;
	use CoreTraits\HasImages, CoreTraits\HasLink, CoreTraits\HasMetadata, CoreTraits\HasParams, CoreTraits\HasState;
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
