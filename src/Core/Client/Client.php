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
 * Client selector.
 *
 * @since  1.0.0
 */
abstract class Client
{
	/**
	 * Retrieve the active client.
	 *
	 * @return  ClientInterface
	 */
	public static function active()
	{
		return \JFactory::getApplication()->isAdmin() ? self::admin() : self::site();
	}

	/**
	 * Retrieve admin client.
	 *
	 * @return  Admin
	 */
	public static function admin()
	{
		return new Administrator;
	}

	/**
	 * Retrieve site client.
	 *
	 * @return  Site
	 */
	public static function site()
	{
		return new Site;
	}
}
