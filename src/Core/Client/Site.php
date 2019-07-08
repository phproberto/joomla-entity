<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Client;

defined('_JEXEC') || die;

/**
 * Frontend client.
 *
 * @since  1.0.0
 */
final class Site extends BaseClient implements ClientInterface
{
	/**
	 * Client identifier.
	 *
	 * @const
	 */
	const ID = 0;

	/**
	 * Client name.
	 *
	 * @const
	 */
	const NAME = 'Site';

	/**
	 * Get the base folder of this client.
	 *
	 * @return  string
	 */
	public function getFolder()
	{
		return JPATH_SITE;
	}
}
