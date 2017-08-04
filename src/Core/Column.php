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
	 * Default column used to store client.
	 *
	 * @const
	 */
	const CLIENT = 'client_id';

	/**
	 * Default column used to store featured.
	 *
	 * @const
	 */
	const FEATURED = 'featured';

	/**
	 * Default column used to store images.
	 *
	 * @const
	 */
	const IMAGES = 'images';

	/**
	 * Default column used to store metadata.
	 *
	 * @const
	 */
	const METADATA = 'metadata';

	/**
	 * Default column used to store params.
	 *
	 * @const
	 */
	const PARAMS = 'params';

	/**
	 * Default column used to store state.
	 *
	 * @const
	 */
	const STATE = 'published';
}
