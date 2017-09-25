<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Validation;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Rule;

/**
 * ArticleValidator validator.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticleValidator extends Validator
{
	/**
	 * Constructor.
	 *
	 * @param   Article  $article  Article to validate.
	 */
	public function __construct(Article $article)
	{
		parent::__construct($article);

		$this->addRules(
			array(
				'title' => new Rule\IsNotEmptyString('Not empty title'),
				'catid' => new Rule\IsPositiveInteger('Valid category identifier')
			)
		);
	}
}
