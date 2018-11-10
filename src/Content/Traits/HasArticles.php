<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Content\Article;

/**
 * Trait for entities that have associated articles.
 *
 * @since  1.0.0
 */
trait HasArticles
{
	/**
	 * Associated articles.
	 *
	 * @var  Collection
	 */
	protected $articles;

	/**
	 * Clear already loaded articles.
	 *
	 * @return  self
	 */
	public function clearArticles()
	{
		$this->articles = null;

		return $this;
	}

	/**
	 * Get the associated articles.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  Collection
	 */
	public function articles($reload = false)
	{
		if ($reload || null === $this->articles)
		{
			$this->articles = $this->loadArticles();
		}

		return $this->articles;
	}

	/**
	 * Check if this entity has an associated article.
	 *
	 * @param   integer   $id  Article identifier
	 *
	 * @return  boolean
	 */
	public function hasArticle($id)
	{
		return $this->articles()->has($id);
	}

	/**
	 * Check if this entity has associated articles.
	 *
	 * @return  boolean
	 */
	public function hasArticles()
	{
		return !$this->articles()->isEmpty();
	}

	/**
	 * Load associated articles from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadArticles();
}
