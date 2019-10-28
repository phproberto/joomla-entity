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
trait HasEntityUpdate
{
	/**
	 * Get an item from its ID.
	 *
	 * @return  void
	 */
	public function ajaxEntityUpdate()
	{
		if (!Request::active()->validateAjaxWithTokenOrCloseApp())
		{
			return;
		}

		$this->jsonEntityUpdate();
	}

	/**
	 * JSON entity update.
	 *
	 * @return  void
	 */
	public function jsonEntityUpdate()
	{
		if (!Request::active()->validateHasToken())
		{
			return;
		}

		$response = new JSONResponse;

		$data = $this->entityUpdateDataFromRequest();
		$context = $this->option . '.entityUpdate.' . $this->context . '.data';
		$entity = $this->entityInstance();

		$isNew = empty($data[$entity->primaryKey()]);
		$entity->bind($data);

		if ($entity instanceof Aclable && !$entity->acl()->canEdit())
		{
			return $response->setStatusCode(403)
				->setStatusText(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'))
				->send();
		}

		try
		{
			$entity->save();
		}
		catch (SaveException $e)
		{
			return $response->setStatusCode(500)
				->setStatusText($e->getMessage())
				->send();
		}
		catch (ValidationException $e)
		{
			return $response->setStatusCode(400)
				->setStatusText($e->getMessage())
				->send();
		}

		return $response->setData($entity->all())->send();
	}

	/**
	 * Retrieve AJAX save data form the request.
	 *
	 * @return  array
	 */
	public function entityUpdateDataFromRequest()
	{
		return $this->input->post->get($this->getModel()->formControl(), [], 'array');
	}

	/**
	 * Save entity data. Method to support legacy methods with session storage + redirections.
	 *
	 * @return  boolean
	 */
	public function entityUpdate()
	{
		Request::active()->validateHasToken();

		$app = Factory::getApplication();
		$data = $this->entityUpdateDataFromRequest();
		$context = $this->option . '.entityUpdate.' . $this->context . '.data';

		$entity = $this->entityInstance();

		$isNew = empty($data[$entity->primaryKey()]);
		$entity->bind($data);

		// Bind owner from active user
		if ($isNew && $entity instanceof Ownerable)
		{
			$entity->assign(
				$entity->columnAlias(UsersColumn::OWNER),
				User::active()->id()
			);
		}

		if ($entity instanceof Aclable && !$entity->acl()->canEdit())
		{
			$app->setUserState($context, $data);

			$error = Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED');
			$this->setMessage($error, 'error');

			return false;
		}

		try
		{
			$entity->save();
		}
		catch (\Exception $e)
		{
			$app->setUserState($context, $data);

			$this->setMessage($e->getMessage(), 'error');

			return false;
		}

		$app->setUserState($context, null);
		$langKey = $this->{'text_prefix'} . ($isNew && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS';
		$prefix  = Factory::getLanguage()->hasKey($langKey) ? $this->{'text_prefix'} : 'JLIB_APPLICATION';

		$this->setMessage(Text::_($prefix . ($isNew  && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));

		return true;
	}


	/**
	 * URL to return if creation works.
	 *
	 * @return  string
	 */
	public function entityUpdateReturnError()
	{
		$url = $this->input->get('returnError', null, 'base64');

		if ($url && Uri::isInternal(base64_decode($url)))
		{
			return base64_decode($url);
		}

		return $this->entityCreateReturnOk();
	}

	/**
	 * URL to return if creation works.
	 *
	 * @return  string
	 */
	public function entityUpdateReturnOk()
	{
		$url = $this->input->get('return', null, 'base64');

		if ($url && Uri::isInternal(base64_decode($url)))
		{
			return base64_decode($url);
		}

		// Do not tie behavior to joomla's controllers but support them
		if (!property_exists($this, 'view_list'))
		{
			return Uri::root();
		}

		$url = 'index.php?option=' . $this->option . '&view=' . $this->{'view_list'};

		// Do not tie behavior to joomla's controllers but support them
		if (method_exists($this, 'getRedirectToListAppend'))
		{
			$url .= $this->getRedirectToListAppend();
		}

		return Route::_($url);
	}
}
