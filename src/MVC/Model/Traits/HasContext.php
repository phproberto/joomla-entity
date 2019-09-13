<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\Traits;

defined('_JEXEC') || die;

/**
 * For models with extended context functions.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasContext
{
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var    string
	 */
	protected $context = null;

	/**
	 * Gets the model context.
	 *
	 * @return  string  The context
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Sets the context.
	 *
	 * @param   string  $context  The context
	 *
	 * @return  void
	 */
	public function setContext($context)
	{
		$this->context = $context;
	}
}
