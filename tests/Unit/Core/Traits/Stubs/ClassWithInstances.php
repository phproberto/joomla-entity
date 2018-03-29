<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\Core\Traits\HasInstances;

/**
 * Sample class to test HasInstances trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ClassWithInstances
{
	use HasInstances;

	/**
	 * Class identifier
	 *
	 * @var  integer
	 */
	protected $id;

	/**
	 * Name property.
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param   integer  $id  Identifier
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * Gets the Class identifier.
	 *
	 * @return  integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Gets the Name property.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the Name property.
	 *
	 * @param   string  $name  the name
	 *
	 * @return self
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}
}
