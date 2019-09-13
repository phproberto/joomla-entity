<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State;

defined('_JEXEC') || die;

/**
 * Represents a model state property.
 *
 * @since  __DEPLOY_VERSION__
 */
interface PropertyInterface
{
	/**
	 * Can this property be populated from request?
	 *
	 * @return  boolean
	 */
	public function isPopulable();

	/**
	 * Retrieve the property identifier.
	 *
	 * @return  string
	 */
	public function key();
}
