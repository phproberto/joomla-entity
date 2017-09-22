<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Column;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have an associated publish up column.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasPublishUp
{
	/**
	 * Get the publish up date.
	 *
	 * @return  string
	 */
	public function getPublishUp()
	{
		return $this->get($this->columnAlias(Column::PUBLISH_UP));
	}

	/**
	 * Has this entity a publish up date?
	 *
	 * @return  boolean
	 */
	public function hasPublishUp()
	{
		$publishUp = $this->getPublishUp();

		return !empty($publishUp) && $publishUp !== $this->nullDate();
	}

	/**
	 * Check if this entity is published up.
	 *
	 * @return  boolean
	 */
	public function isPublishedUp()
	{
		if (!$this->hasPublishUp())
		{
			return true;
		}

		return \JFactory::getDate($this->getPublishUp()) <= \JFactory::getDate();
	}

	/**
	 * Get the empty date for the active DB driver.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	protected function nullDate()
	{
		return \JFactory::getDbo()->getNullDate();
	}
}
