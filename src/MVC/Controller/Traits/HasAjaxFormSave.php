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
use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Helper\ArrayHelper;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;

/**
 * For controllers with save method to process form save.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAjaxFormSave
{
	/**
	 * Get an item from its ID.
	 *
	 * @return  void
	 */
	public function ajaxFormSave()
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

			return;
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

			return;
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

			return;
		}

		$app->setUserState($context . '.data', null);
		$app->sendHeaders();
		echo json_encode($model->getState($this->context . '.id'));
		$app->close();
	}
}
