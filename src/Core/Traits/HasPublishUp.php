<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\CoreColumn;

/**
 * Trait for entities that have an associated publish up column.
 *
 * @since  1.0.0
 */
trait HasPublishUp
{
	/**
	 * Get the empty date for the active DB driver.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	abstract protected function nullDate();

	/**
	 * Get the publish up date.
	 *
	 * @return  string
	 */
	public function getPublishUp()
	{
		return $this->get($this->columnAlias(CoreColumn::PUBLISH_UP));
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
}
