<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
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
 * @since  1.0.0
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
			[
				'title'  => new Rule\IsNotEmptyString('Not empty title'),
				'catid'  => new Rule\IsPositiveInteger('Valid category identifier'),
				'access' => new Rule\IsNullOrPositiveInteger('Valid view level identifier')
			]
		);
	}
}
