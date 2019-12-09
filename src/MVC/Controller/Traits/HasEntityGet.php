<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller\Traits;

use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\MVC\JSONResponse;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;

defined('_JEXEC') || die;

/**
 * For controllers with entity read method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntityGet
{
	/**
	 * Get an item from its ID.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	public function ajaxEntityGet(string $method = 'get')
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp($method))
		{
			return;
		}

		return $this->jsonEntityCreate();
	}

	/**
	 * Retrieve an instance of the associated entity.
	 *
	 * @param   integer  $id  Identifier
	 *
	 * @return  EntityInterface
	 */
	abstract public function entityInstance(int $id = null);

	/**
	 * JSON entity update.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	public function jsonEntityGet(string $method = 'post')
	{
		if (!Request::active()->validateHasToken($method))
		{
			return;
		}

		$response = new JSONResponse;

		$id = $this->input->getInt($this->entityInstance()->primaryKey());

		if (!$id)
		{
			return $response->setStatusCode(400)
				->setErrorMessage(Text::_($this->{'text_prefix'} . '_ERROR_NO_ITEM_SELECTED'))
				->send();

			return;
		}

		try
		{
			$entity = $this->loadEntityFromRequest($id);
		}
		catch (\Exception $e)
		{
			return $response->setStatusCode(404)
				->setErrorMessage(Text::_($this->{'text_prefix'} . '_ERROR_ITEM_NOT_FOUND'))
				->send();
		}

		if ($entity instanceof Aclable && !$entity->acl()->canView())
		{
			return $response->setStatusCode(403)
				->setErrorMessage(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'))
				->send();
		}

		return $response->setData($entity->all())->send();
	}
}
