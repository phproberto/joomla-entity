<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Translation;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Core\CoreColumn;
use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Validation\Rule\IsNotNull;
use Phproberto\Joomla\Entity\Translation\Contracts\Translatable;
use Phproberto\Joomla\Entity\Translation\Contracts\Translator as TranslatorContract;
use Phproberto\Joomla\Entity\Validation\Contracts\Validator as ValidatorContract;

/**
 * Entity translation.
 *
 * @since   1.0.0
 */
class Translator extends Decorator implements TranslatorContract
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
	 * Translation validator.
	 *
	 * @var  Validator
	 */
	protected $validator;

	/**
	 * Constructor.
	 *
	 * @param   Translatable  $entity   Entity to decorate.
	 * @param   string        $langTag  Language tag. Example: es-ES
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
	 * Check if entity language is the translation language.
	 *
	 * @return  boolean
	 */
	public function isEntityLanguage()
	{
		$languageColumn = $this->entity->columnAlias(CoreColumn::LANGUAGE);

		return $this->entity->get($languageColumn) === $this->langTag;
	}

	/**
	 * Set the active validator for the translations.
	 *
	 * @param   ValidatorContract  $validator  Desired validator
	 *
	 * @return  self
	 */
	public function setValidator(ValidatorContract $validator)
	{
		$this->validator = $validator;

		return $this;
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
		$value = $this->isEntityLanguage() ? $this->entity->get($column) : $this->translation()->get($column);

		if ($this->validator()->isValidColumnValue($column, $value))
		{
			return $value;
		}

		return $default;
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

	/**
	 * Retrieve the translation validator.
	 *
	 * @return  ValidatorContract
	 */
	public function validator()
	{
		if (null === $this->validator)
		{
			$this->validator = new Validator($this->entity);
			$this->validator->addGlobalRule(new IsNotNull);
		}

		return $this->validator;
	}
}
