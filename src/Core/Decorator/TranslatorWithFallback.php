<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Decorator;

/**
 * Represents a collection of entities.
 *
 * @since   __DEPLOY_VERSION__
 */
class TranslatorWithFallback extends Translator
{
	/**
	 * Translate a column if a condition is met.
	 *
	 * @param   callable  $condition  Condition to apply
	 * @param   string    $column     Column to translate
	 * @param   mixed     $default    Default value
	 *
	 * @return  mixed
	 */
	public function translateIf(callable $condition, $column, $default = null)
	{
		$value = $this->translation()->get($column);

		if ($condition($value))
		{
			return $value;
		}

		$value = $this->entity->get($column);

		return $condition($value) ? $value : $default;
	}
}
