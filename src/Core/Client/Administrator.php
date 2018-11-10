<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Client;

defined('_JEXEC') || die;

/**
 * Backend client.
 *
 * @since  1.0.0
 */
final class Administrator extends BaseClient implements ClientInterface
{
	/**
	 * Client identifier.
	 *
	 * @const
	 */
	const ID = 1;

	/**
	 * Client name.
	 *
	 * @const
	 */
	const NAME = 'Administrator';

	/**
	 * Get the base folder of this client.
	 *
	 * @return  string
	 */
	public function getFolder()
	{
		return JPATH_ADMINISTRATOR;
	}
}
