<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Contracts;

/**
 * Publishable entities requirements.
 *
 * @since   __DEPLOY_VERSION__
 */
interface Publishable
{
	/**
	 * Check if this entity is published.
	 *
	 * @return  boolean
	 */
	public function isPublished();

	/**
	 * Check if this entity is unpublished.
	 *
	 * @return  boolean
	 */
	public function isUnpublished();
}
