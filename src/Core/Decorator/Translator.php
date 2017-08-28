<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Decorator;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Core\Contracts\Translator as TranslatorInterface;
use Phproberto\Joomla\Entity\Core\Contracts\Translatable;

/**
 * Entity translation.
 *
 * @since   __DEPLOY_VERSION__
 */
class Translator extends Decorator implements TranslatorInterface
{
	/**
	 * Translation language tag.
	 *
	 * @var  string
	 */
	protected $langTag;

	/**
	 * Entity translation.
	 *
	 * @var  EntityInterface
	 */
	protected $translation;

	/**
	 * Constructor.
	 *
	 * @param   EntityInterface  $entity   Entity to decorate.
	 * @param   string           $langTag  Language tag. Example: es-ES
	 */
	public function __construct(Translatable $entity, $langTag = null)
	{
		parent::__construct($entity);

		$this->langTag = $langTag ?: $this->activeLanguage()->getTag();
	}

	/**
	 * Get the active language.
	 *
	 * @return  \Joomla\CMS\Language\Language
	 *
	 * @codeCoverageIgnore
	 */
	public function activeLanguage()
	{
		return \JFactory::getLanguage();
	}

	/**
	 * Values that will cause translation to return default.
	 *
	 * @return  boolean
	 */
	protected function emptyValues()
	{
		return array(
			null, '', $this->nullDate()
		);
	}

	/**
	 * Get the empty driver for the active DB driver.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function nullDate()
	{
		return \JFactory::getDbo()->getNullDate();
	}

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
		$condition = function ($value) {
			return !in_array($value, $this->emptyValues(), true);
		};

		return $this->translateIf($condition, $column, $default);
	}

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

		return $condition($value) ? $value : $default;
	}

	/**
	 * Retrieve translation entity.
	 *
	 * @return  EntityInterface
	 */
	protected function translation()
	{
		return $this->entity->translation($this->langTag);
	}
}
