<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Searcher\Stubs;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Searcher\BaseSearcher;

/**
 * Category searcher tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class Searcher extends BaseSearcher
{
	/**
	 * Default options to initialise searcher.
	 *
	 * @return  array
	 */
	public function defaultOptions()
	{
		return array_merge(
			parent::defaultOptions(),
			[
				'option'         => 'default-value',
				'another-option' => 'another-default-value'
			]
		);
	}
}
