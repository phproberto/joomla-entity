<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

use Phproberto\Joomla\Entity\Collection;

/**
 * Trait for entities with translations.
 *
 * @since   __DEPLOY_VERSION__
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
	 * Get the name of the column that stores language tag.
	 *
	 * @return  string
	 */
	protected function columnLanguage()
	{
		return $this->table()->getColumnAlias('language');
	}

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
	 * Retrieve translations indexed by tag.
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
				$tag = $translation->get($this->columnLanguage());
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
