<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Translation\Contracts;

defined('_JEXEC') || die;

/**
 * Describes methods required by translatable entities.
 *
 * @since  __DEPLOY_VERSION__
 */
interface Translator
{
	/**
	 * Translate a column.
	 *
	 * @param   string  $column   Column to translate
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function translate($column, $default = null);
}
