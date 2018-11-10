<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Translation;

defined('_JEXEC') || die;

/**
 * Represents a collection of entities.
 *
 * @since   1.0.0
 */
class TranslatorWithFallback extends Translator
{
	/**
	 * Translate a column.
	 *
	 * @param   string  $column   Column to translate
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function translate($column, $default = null)
	{
		$value = $this->isEntityLanguage() ? $this->entity->get($column) : $this->translation()->get($column);

		if ($this->validator()->isValidColumnValue($column, $value))
		{
			return $value;
		}

		if ($this->isEntityLanguage())
		{
			return $default;
		}

		$value = $this->entity->get($column);

		$isValid = $this->validator()->isValidColumnValue($column, $value);

		return $isValid ? $value : $default;
	}
}
