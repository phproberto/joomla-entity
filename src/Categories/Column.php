<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories;

defined('_JEXEC') || die;

/**
 * Columns supported by categories.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Column
{
	/**
	 * Default column used to store category.
	 *
	 * @const
	 */
	const CATEGORY = 'category_id';
}
