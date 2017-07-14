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
 * Trait for entities that have an associated article.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasArticle
{
	/**
	 * Associated article.
	 *
	 * @var  Article
	 */
	protected $article;

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function all();

	/**
	 * Get the associated article.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  Article
	 */
	public function getArticle($reload = false)
	{
		if ($reload || null === $this->article)
		{
			$this->article = $this->loadArticle();
		}

		return $this->article;
	}

	/**
	 * Get the name of the column that stores article.
	 *
	 * @return  string
	 */
	protected function getColumnArticle()
	{
		return 'article_id';
	}

	/**
	 * Check if this entity has an associated article.
	 *
	 * @return  boolean
	 */
	public function hasArticle()
	{
		return $this->getArticle()->hasId();
	}

	/**
	 * Load the article from the database.
	 *
	 * @return  Article
	 */
	protected function loadArticle()
	{
		$column = $this->getColumnArticle();
		$data = $this->all();

		if (array_key_exists($column, $data))
		{
			return Article::instance($data[$column]);
		}

		return new Article;
	}
}
