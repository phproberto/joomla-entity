<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Stubs;

use Phproberto\Joomla\Entity\Entity as BaseEntity;

/**
 * Stub to test Entity class.
 *
 * @since   1.1.0
 *
 * @codeCoverageIgnore
 */
class Entity extends BaseEntity
{
	/**
	 * Sample public property for tests.
	 *
	 * @var  mixed
	 */
	public $publicProperty;

	/**
	 * Allow to mock table returned by this entity.
	 *
	 * @var  \PHPUnit_Framework_MockObject_MockObject
	 */
	public static $tableMock;

	/**
	 * Get a table.
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
		if (null !== static::$tableMock)
		{
			return static::$tableMock;
		}

		return parent::table($name, $prefix, $options);
	}
}
