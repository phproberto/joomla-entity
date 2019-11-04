<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\View;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\View\HTMLView;

/**
 * Base form view.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class FormView extends HTMLView
{
	/**
	 * Load layout data.
	 *
	 * @return  self
	 */
	protected function loadLayoutData()
	{
		$model = $this->getModel();

		return array_merge(
			parent::loadLayoutData(),
			[
				'form'  => $model->getForm(),
				'model' => $model
			]
		);
	}
}
