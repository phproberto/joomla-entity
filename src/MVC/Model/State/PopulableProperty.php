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
 * Represents a state property.
 *
 * @since  __DEPLOY_VERSION__
 */
class PopulableProperty extends Property
{
	/**
	 * Can this property be populated from request?
	 *
	 * @var  boolean
	 */
	protected $isPopulable = true;
}
