<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Messages;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\ComponentEntity;

/**
 * Message entity.
 *
 * @since   1.6.0
 */
class Message extends ComponentEntity
{
	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function primaryKey()
	{
		return 'message_id';
	}

	/**
	 * Get a table instance. Defauts to \JTableUser.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/tables');

		$name   = $name ?: 'Message';
		$prefix = $prefix ?: 'MessagesTable';

		return parent::table($name, $prefix, $options);
	}
}
