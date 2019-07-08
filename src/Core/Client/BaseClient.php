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
 * Base client.
 *
 * @since  1.0.0
 */
abstract class BaseClient
{
	/**
	 * Client identifier.
	 *
	 * @var  integer
	 */
	protected $id;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->id = static::ID;
	}

	/**
	 * Get client identifier.
	 *
	 * @return  integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get client name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return static::NAME;
	}

	/**
	 * Is this admin client?
	 *
	 * @return  boolean
	 */
	public function isAdmin()
	{
		return $this->id === Administrator::ID;
	}

	/**
	 * Is this site client?
	 *
	 * @return  boolean
	 */
	public function isSite()
	{
		return $this->id === Site::ID;
	}
}
