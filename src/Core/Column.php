<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core;

/**
 * Columns supported by core.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Column
{
	/**
	 * Default column used to store access.
	 *
	 * @const
	 */
	const ACCESS = 'access';

	/**
	 * Default column used to store asset.
	 *
	 * @const
	 */
	const ASSET = 'asset_id';

	/**
	 * Default column used to store metadata.
	 *
	 * @const
	 */
	const METADATA = 'metadata';
}
