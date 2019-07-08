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
 * Describes methods required by modules.
 *
 * @since  0.0.1
 */
interface ClientInterface
{
	/**
	 * Get the base folder of this client.
	 *
	 * @return  string
	 */
	public function getFolder();

	/**
	 * Get the identifier.
	 *
	 * @return  integer
	 */
	public function getId();

	/**
	 * Get the name
	 *
	 * @return  string
	 */
	public function getName();

	/**
	 * Is this admin client?
	 *
	 * @return  boolean
	 */
	public function isAdmin();

	/**
	 * Is this site client?
	 *
	 * @return  boolean
	 */
	public function isSite();
}
