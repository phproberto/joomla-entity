<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories\Validation;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Rule;

/**
 * Category validator.
 *
 * @since  1.7.0
 */
class CategoryValidator extends Validator
{
	/**
	 * Constructor.
	 *
	 * @param   Article  $article  Article to validate.
	 */
	public function __construct(Category $article)
	{
		parent::__construct($article);

		$this->addRules(
			[
				'access'    => new Rule\IsNullOrPositiveInteger('Valid view level identifier'),
				'extension' => new Rule\IsNotEmptyString('Not empty extension'),
				'level'     => new Rule\IsNullOrPositiveInteger('Valid level'),
				'parent_id' => new Rule\IsNullOrPositiveInteger('Valid parent'),
				'title'     => new Rule\IsNotEmptyString('Not empty title')
			]
		);
	}
}
