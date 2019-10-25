<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Traits;
use Phproberto\Joomla\Entity\ComponentEntity;

/**
 * Represents and entry from the #__modules table.
 *
 * @since   1.4.0
 */
class Module extends ComponentEntity
{
	use Traits\HasAccess, Traits\HasAsset, Traits\HasClient, Traits\HasParams, Traits\HasPublishDown, Traits\HasPublishUp, Traits\HasState;

	/**
	 * Load this module assigned menu ids.
	 *
	 * @var  array
	 */
	private $menusIds;

	/**
	 * Load assigned menus ids from database.
	 *
	 * @return  int[]
	 */
	private function loadMenusIds()
	{
		if (!$this->hasId())
		{
			return [];
		}

		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('menuid')
			->from($db->qn('#__modules_menu'))
			->where($db->qn('moduleid') . ' = ' . (int) $this->id());

		$db->setQuery($query);

		return array_map('intval', $db->loadColumn() ?: []);
	}

	/**
	 * Get the menus this module is shown.
	 *
	 * @param   boolean  $reload  Force to reload data from DB.
	 *
	 * @return  array
	 */
	public function menusIds($reload = false)
	{
		if ($reload || null === $this->menusIds)
		{
			$this->menusIds = $this->loadMenusIds();
		}

		return $this->menusIds;
	}

	/**
	 * Check if this entity is published.
	 *
	 * @return  boolean
	 */
	public function isPublished()
	{
		if (!$this->isOnState(self::STATE_PUBLISHED))
		{
			return false;
		}

		return $this->isPublishedUp() && !$this->isPublishedDown();
	}

	/**
	 * Check if this module is published on a specific menu item.
	 *
	 * @param   integer  $menuId  Menu identifier
	 *
	 * @return  boolean
	 */
	public function isPublishedInMenu($menuId)
	{
		$menuId = (int) $menuId;
		$menusIds = $this->menusIds();

		if (in_array(0, $menusIds, true))
		{
			return true;
		}

		if (!$menusIds || 0 === $menuId)
		{
			return false;
		}

		$assignedMenuId = reset($menusIds);

		return $assignedMenuId > 0 ? in_array($menuId, $menusIds, true) : !in_array(-1 * $menuId, $menusIds, true);
	}


	/**
	 * Check if this entity is unpublished.
	 *
	 * @return  boolean
	 */
	public function isUnpublished()
	{
		return !$this->isPublished();
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\Table\Table
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name = $name ?: 'Module';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}
}
