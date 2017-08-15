<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

/**
 * Columns supported by users.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Column
{
	/**
	 * Default column used to store author.
	 *
	 * @const
	 */
	const AUTHOR = 'created_by';

	/**
	 * Default column used to store editor.
	 *
	 * @const
	 */
	const EDITOR = 'modified_by';
}
