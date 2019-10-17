<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController as BaseFormController;
use Joomla\CMS\Uri\Uri;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasAssociatedEntity;

/**
 * Base Form Controller.
 *
 * @since  1.0.1
 */
abstract class FormController extends BaseFormController
{
	use HasAssociatedEntity;

	/**
	 * Retrieve item information through AJAX.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function ajaxGet()
	{
		Request::active()->validateAjaxWithTokenOrCloseApp('get');

		$app = Factory::getApplication();

		try
		{
			$entity = $this->loadEntityFromRequest();
		}
		catch (Exception $e)
		{
			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo Text::_($this->{'text_prefix'} . '_NO_ITEM_SELECTED');
			$app->close();
		}

		$entity = $this->loadEntityFromRequest();

		if ($entity instanceof Aclable && !$entity->acl()->canView())
		{
			$app->setHeader('status', 403);
			$app->sendHeaders();
			echo Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			$app->close();
		}

		$app->sendHeaders();
		echo json_encode(
			$this->publicEntityData($entity)
		);
		$app->close();
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function ajaxDelete()
	{
		Request::active()->validateAjaxWithTokenOrCloseApp();

		$app = Factory::getApplication();

		$id = $this->input->getInt('id');

		if (!$id)
		{
			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo Text::_($this->{'text_prefix'} . '_NO_ITEM_SELECTED');
			$app->close();
		}

		if (!$this->allowDelete($id))
		{
			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED');
			$app->close();
		}

		// Remove the items.
		$cid = [$id];

		try
		{
			$entityClass = $this->entityClass();
			$entityClass::delete($cid);
		}
		catch (\Exception $e)
		{
			$app->setHeader('status', '500');
			$app->sendHeaders();
			echo $e->getMessage();
			$app->close();
		}

		$app->sendHeaders();
		echo $id;
		$app->close();
	}

	/**
	 * Get an item from its ID.
	 *
	 * @return  void
	 */
	public function ajaxSave()
	{
		Request::active()->validateAjaxWithTokenOrCloseApp();

		$app = Factory::getApplication();

		$data = $this->getFormDataFromRequest();
		$context = $this->option . '.edit.' . $this->context;

		$model = $this->getModel();

		$form = $model->getForm($data, false);

		$data = $form->filter($data);
		$validationResult = $form->validate($data);

		// Check for an error.
		if ($validationResult instanceof \Exception)
		{
			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo json_encode(
				[
					$validationResult->getMessage()
				]
			);
			$app->close();
		}

		if ($validationResult === false)
		{
			$response = [];

			foreach ($form->getErrors() as $key => $message)
			{
				if ($message instanceof \Exception)
				{
					$response[$key] = $message->getMessage();
					continue;
				}

				$response[$key] = $message;
			}

			$app->setUserState($context . '.data', $data);

			$app->setHeader('status', '400');
			$app->sendHeaders();
			echo json_encode($response);
			$app->close();
		}

		if (!$model->save($data))
		{
			$app->setUserState($context . '.data', $data);

			$app->setHeader('status', '500');
			$app->sendHeaders();
			echo json_encode(
				[
					Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError())
				]
			);
			$app->close();
		}

		$app->setUserState($context . '.data', null);
		$app->sendHeaders();
		echo json_encode($model->getState($this->context . '.id'));
		$app->close();
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array())
	{
		$entityClass = $this->entityClass();
		$entity = new $entityClass;

		if (!$entity instanceof Aclable)
		{
			return parent::allowAdd($data);
		}

		$entity->bind($data);

		if ($entity instanceof Ownerable)
		{
			$ownerColumn = $entity->columnAlias(UsersColumn::OWNER);

			$userId = $this->input->getInt($ownerColumn, User::active()->id());
			$entity->assign($ownerColumn, $userId);
		}

		return $entity->acl()->canCreate();
	}

	/**
	 * Method to check if you can delete an item?
	 *
	 * @param   integer  $id  ID of the record to delete
	 *
	 * @return  boolean
	 */
	protected function allowDelete($id)
	{
		if ($id)
		{
			$entityClass = $this->entityClass();

			try
			{
				$entity = $entityClass::load($id);
			}
			catch (\Exception $e)
			{
				return false;
			}

			if ($entity instanceof Aclable)
			{
				return $entity->acl()->canDelete();
			}
		}

		$user = Factory::getUser();

		return $user->authorise('core.delete', $this->option);
	}

	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$id = empty($data[$key]) ? 0 : $data[$key];

		if (!$id || !$this instanceof AssociatedEntity)
		{
			return parent::allowEdit($data, $key);
		}

		$entityClass = $this->entityClass();
		$entity = $entityClass::load($id);

		if (!$entity instanceof Aclable)
		{
			return parent::allowEdit($data, $key);
		}

		if ($entity instanceof Ownerable)
		{
			$ownerColumn = $entity->columnAlias(UsersColumn::OWNER);

			$userId = $this->input->getInt($ownerColumn, User::active()->id());
			$entity->assign($ownerColumn, $userId);
		}

		$entity->bind($data);

		return $entity->acl()->canEdit();
	}

	/**
	 * Removes an item.
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		// Check for request forgeries
		$this->checkToken('get');

		// Get items to remove from the request.
		$id = $this->input->getInt('id');
		$return = $this->input->get('return', null, 'base64');

		$this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->{'view_list'}, false));

		if (!$id)
		{
			$error = Text::_($this->{'text_prefix'} . '_NO_ITEM_SELECTED');
			\JLog::add($error, \JLog::WARNING, 'jerror');
			$this->setMessage($error, 'error');

			return false;
		}

		if (!$this->allowDelete($id))
		{
			$this->setMessage(
				Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'),
				'error'
			);

			return false;
		}

		// Remove the items.
		$cid = [$id];

		try
		{
			$entityClass = $this->entityClass();
			$entityClass::delete($cid);
		}
		catch (\Exception $e)
		{
			$this->setMessage($e->getMessage(), 'error');

			return false;
		}

		if (!is_null($return) && Uri::isInternal(base64_decode($return)))
		{
			$this->setRedirect(base64_decode($return));
		}

		$this->setMessage(Text::plural($this->{'text_prefix'} . '_N_ITEMS_DELETED', count($cid)));

		return true;
	}

	/**
	 * Retrieve public entity data to send it in responses. Method to remove private data, etc.
	 *
	 * @param   EntityInterface  $entity  Entity whose information we want to show
	 *
	 * @return  array
	 *
	 * @since   1.1.0
	 */
	protected function publicEntityData(EntityInterface $entity) : array
	{
		return $entity->all();
	}

	/**
	 * Get the form data from the request.
	 *
	 * @return  array
	 *
	 * @since   1.0.7
	 */
	protected function getFormDataFromRequest()
	{
		return $this->input->post->get($this->getModel()->formControl(), [], 'array');
	}

	/**
	 * Gets the name of the latest extending class.
	 * For a class named ContentControllerArticles will return Articles
	 *
	 * @return  string
	 */
	public function instanceName()
	{
		$class = get_class($this);

		if (false !== strpos($class, '\\'))
		{
			return (new \ReflectionClass($this))->getShortName();
		}

		$name = strstr($class, 'Controller');
		$name = str_replace('Controller', '', $name);

		return strtolower($name);
	}
}
