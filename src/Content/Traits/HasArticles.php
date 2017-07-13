<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Content\Traits;

use Phproberto\Joomla\Entity\Content\Article;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have associated articles.
 *
 * @since  __DEPLOY_VERSION__
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
	public function getArticles($reload = false)
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
		return $this->getArticles()->has($id);
	}

	/**
	 * Check if this entity has associated articles.
	 *
	 * @return  boolean
	 */
	public function hasArticles()
	{
		return !$this->getArticles()->isEmpty();
	}

	/**
	 * Load associated articles from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadArticles();
}
