<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

/**
 * Columns supported by users.
 *
 * @since   1.0.0
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

	/**
	 * Default column used to store owner.
	 *
	 * @const
	 */
	const OWNER = 'created_by';

	/**
	 * Default column used to store user.
	 *
	 * @const
	 */
	const USER = 'user_id';
}
