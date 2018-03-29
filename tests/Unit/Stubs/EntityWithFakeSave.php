<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Stubs;

use Phproberto\Joomla\Entity\Entity as BaseEntity;

/**
 * Stub to test Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityWithFakeSave extends BaseEntity
{
	/**
	 * Save method executed?
	 *
	 * @var  boolean
	 */
	public $saved = false;

	/**
	 * Data saved in save method.
	 *
	 * @var  array
	 */
	public $savedData = array();

	/**
	 * Save entity to the database.
	 *
	 * @return  self
	 *
	 * @throws  SaveException
	 */
	public function save()
	{
		$this->savedData = $this->row;
		$this->saved = true;

		return $this;
	}
}
