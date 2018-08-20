<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Traits;
use Phproberto\Joomla\Entity\ComponentEntity;

/**
 * Represents and entry from the #__modules table.
 *
 * @since   __DEPLOY_VERSION__
 */
class Module extends ComponentEntity
{
	use Traits\HasAccess, Traits\HasAsset, Traits\HasClient, Traits\HasParams, Traits\HasPublishDown, Traits\HasPublishUp, Traits\HasState;

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
	 * @return  \JTable
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
