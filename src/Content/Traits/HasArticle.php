<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Content\Article;

/**
 * Trait for entities that have an associated article.
 *
 * @since  1.0.0
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
			return Article::find($data[$column]);
		}

		return new Article;
	}
}
