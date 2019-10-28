<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller\Traits;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;

defined('_JEXEC') || die;

/**
 * For controllers with entity read method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntityRead
{
	/**
	 * Retrieve an instance of the associated entity.
	 *
	 * @param   integer  $id  Identifier
	 *
	 * @return  EntityInterface
	 */
	abstract public function entityInstance(int $id = null);

	/**
	 * Entity read
	 *
	 * @return  void
	 */
	public function entityRead()
	{
		Request::active()->validateHasToken('get');
		$app = Factory::getApplication();

		$id = $this->input->getInt($this->entityInstance()->primaryKey());

		if (!$id)
		{
			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo Text::_($this->{'text_prefix'} . '_NO_ITEM_SELECTED');
			$app->close();

			return;
		}

		$entity = $this->entityInstance($id);

		if ($entity instanceof Aclable && !$entity->acl()->canView())
		{
			$app->setHeader('status', 403);
			$app->sendHeaders();
			echo Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			$app->close();

			return;
		}

		$app->sendHeaders();
		echo json_encode($entity->all());
		$app->close();
	}
}
