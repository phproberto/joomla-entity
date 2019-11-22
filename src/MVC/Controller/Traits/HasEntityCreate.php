<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\MVC\JSONResponse;
use Phproberto\Joomla\Entity\Helper\ArrayHelper;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Exception\SaveException;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * For controllers with ajax save method.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasEntityCreate
{
	/**
	 * Get an item from its ID.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	public function ajaxEntityCreate(string $method = 'post')
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp($method))
		{
			return;
		}

		return $this->jsonEntityCreate();
	}

	/**
	 * JSON entity update.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  void
	 */
	public function jsonEntityCreate(string $method = 'post')
	{
		if (!Request::active()->validateHasToken($method))
		{
			return;
		}

		$response = new JSONResponse;

		$data = $this->entityCreateDataFromRequest();
		$entity = $this->entityInstance();

		$isNew = empty($data[$entity->primaryKey()]);
		$entity->bind($data);

		if ($isNew && $entity instanceof Ownerable)
		{
			$entity->assign(
				$entity->columnAlias(UsersColumn::OWNER),
				User::active()->id()
			);
		}

		if ($entity instanceof Aclable && !$entity->acl()->canCreate())
		{
			return $response->setStatusCode(403)
				->setErrorMessage(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'))
				->send();
		}

		try
		{
			$entity->save();
		}
		catch (SaveException $e)
		{
			return $response->setStatusCode(500)
				->setErrorMessage($e->getMessage())
				->send();
		}
		catch (ValidationException $e)
		{
			return $response->setStatusCode(400)
				->setErrorMessage($e->getMessage())
				->send();
		}

		return $response->setData($entity->all())->send();
	}

	/**
	 * Retrieve AJAX save data form the request.
	 *
	 * @return  array
	 */
	public function entityCreateDataFromRequest()
	{
		return $this->input->post->get('entity', [], 'array');
	}

	/**
	 * Retrieve an instance of the associated entity.
	 *
	 * @param   integer  $id  Identifier
	 *
	 * @return  EntityInterface
	 */
	abstract public function entityInstance(int $id = null);
}
