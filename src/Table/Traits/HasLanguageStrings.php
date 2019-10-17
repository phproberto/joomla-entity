<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */
namespace Phproberto\Joomla\Entity\Table\Traits;

defined('_JEXEC') || die;

/**
 * Tables with language strings.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasLanguageStrings
{
	/**
	 * Text prefix for language strings.
	 *
	 * @var  string
	 */
	protected $textPrefix;

	/**
	 * Get the text prefix to use for language strings
	 *
	 * @return  string
	 */
	public function getTextPrefix()
	{
		if (empty($this->textPrefix))
		{
			$this->textPrefix = 'LIB_' . strtoupper($this->getInstancePrefix()) . '_' . strtoupper($this->getInstanceName());
		}

		return $this->textPrefix;
	}
}
