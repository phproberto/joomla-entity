<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Represents a collection of entities.
 *
 * @since   1.0.0
 */
abstract class Decorator
{
	/**
	 * Decorated entity.
	 *
	 * @var  EntityInterface
	 */
	protected $entity;

	/**
	 * Constructor.
	 *
	 * @param   EntityInterface  $entity  Entity to decorate.
	 */
	public function __construct(EntityInterface $entity)
	{
		$this->entity = $entity;
	}
}
