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
class Property implements PropertyInterface
{
	/**
	 * Property identifier.
	 *
	 * @var  string
	 */
	protected $key;

	/**
	 * Can this property be populated from request?
	 *
	 * @var  boolean
	 */
	protected $isPopulable = false;

	/**
	 * Constructor
	 *
	 * @param   string  $key  Property identifier. Example: list.limit
	 *
	 * @throws  \RuntimeException
	 */
	public function __construct($key)
	{
		$key = trim($key);

		if (empty($key))
		{
			throw new \RuntimeException('Missing key for model state property');
		}

		$this->key = $key;
	}

	/**
	 * Retrieve the property identifier.
	 *
	 * @return  string
	 */
	public function key()
	{
		return $this->key;
	}

	/**
	 * Can this property be populated from request?
	 *
	 * @return  boolean
	 */
	public function isPopulable()
	{
		return $this->isPopulable;
	}
}
