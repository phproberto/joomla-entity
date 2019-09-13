<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\QueryModifier\QueryModifierInterface;

/**
 * Trait for models with query modifiers.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasQueryModifiers
{
	/**
	 * Apply a query modifier.
	 *
	 * @param   QueryModifierInterface  $modifier  [description]
	 *
	 * @return  void
	 */
	public function applyQueryModifier(QueryModifierInterface $modifier)
	{
		$modifier->apply();
	}

	/**
	 * Apply an array of query modifiers.
	 *
	 * @param   QueryModifierInterface[]  $modifiers  Query modifiers to apply
	 *
	 * @return  void
	 */
	public function applyQueryModifiers(array $modifiers)
	{
		foreach ($modifiers as $modifier)
		{
			$modifier->apply();
		}
	}
}
