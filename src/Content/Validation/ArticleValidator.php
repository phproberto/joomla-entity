<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Validation;

use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Rule\NotEmptyString;
use Phproberto\Joomla\Entity\Validation\Rule\PositiveInteger;

defined('JPATH_PLATFORM') || die;

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

		$this->addRule(new NotEmptyString, array('title'), 'Not empty title');
		$this->addRule(new PositiveInteger, array('catid'), 'Valid category identifier');
	}
}
