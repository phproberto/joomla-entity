<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Translation\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Core\Column;

/**
 * Trait for entities with translations.
 *
 * @since   1.0.0
 */
trait HasTranslations
{
	/**
	 * Associated translations.
	 *
	 * @var  Collection
	 */
	protected $translations;

	/**
	 * Associate translations ordered by tag (key).
	 *
	 * @var  array
	 */
	protected $translationsByTag;

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Check if this entity has an associated translation.
	 *
	 * @param   string  $langTag  Language. Example: es-ES
	 *
	 * @return  boolean
	 */
	public function hasTranslation($langTag)
	{
		$translations = $this->translationsByTag();

		return isset($translations[$langTag]);
	}

	/**
	 * Check if this entity has associated translations.
	 *
	 * @return  boolean
	 */
	public function hasTranslations()
	{
		return !$this->translations()->isEmpty();
	}

	/**
	 * Get a translation.
	 *
	 * @param   string  $langTag  Language string. Example: es-ES
	 *
	 * @return  static
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function translation($langTag)
	{
		if (!$this->hasTranslation($langTag))
		{
			$msg = sprintf('Article %d does not have %s language', $this->id(), $langTag);

			throw new \InvalidArgumentException($msg);
		}

		return $this->translationsByTag[$langTag];
	}

	/**
	 * Get the associated translations.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  Collection
	 */
	public function translations($reload = false)
	{
		if ($reload || null === $this->translations)
		{
			$this->translations = $this->loadTranslations();
		}

		return $this->translations;
	}

	/**
	 * Retrieve an array with the available translations using language tag as key.
	 *
	 * @return  array
	 */
	public function translationsByTag()
	{
		if (null === $this->translationsByTag)
		{
			$this->translationsByTag = array();

			foreach ($this->translations() as $translation)
			{
				$tag = $translation->get($this->columnAlias(Column::LANGUAGE));
				$this->translationsByTag[$tag] = $translation;
			}
		}

		return $this->translationsByTag;
	}

	/**
	 * Load associated translations from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadTranslations();
}
