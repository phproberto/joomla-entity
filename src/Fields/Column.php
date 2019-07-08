<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Fields;

defined('_JEXEC') || die;

/**
 * Columns supported by fields.
 *
 * @since   1.2.0
 */
abstract class Column
{
	/**
	 * Default column used to store field group.
	 *
	 * @const
	 */
	const FIELD_GROUP = 'group_id';
}
