<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Helper;

defined('_JEXEC') || die;

/**
 * Utility class for common stuff with arrays.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ArrayHelper
{
	/**
	 * Function to convert array to valid db identifiers values
	 *
	 * @param   array  $array  Array or single value to convert
	 *
	 * @return  array
	 */
	public static function toPositiveIntegers(array $array): array
	{
		return array_values(
			array_unique(
				array_filter(
					array_map('intval', $array),
					function ($value)
					{
						return (int) $value > 0;
					}
				)
			)
		);
	}
}
