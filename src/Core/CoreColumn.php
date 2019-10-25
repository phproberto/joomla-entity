<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core;

defined('_JEXEC') || die;

/**
 * Columns supported by core.
 *
 * @since   1.0.0
 */
abstract class CoreColumn
{
	/**
	 * Default column used to store access.
	 *
	 * @const
	 */
	const ACCESS = 'access';

	/**
	 * Default column used to store access.
	 *
	 * @const
	 * @since  __DEPLOY_VERSION__
	 */
	const ALIAS = 'alias';

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
	 * Default column used to store language.
	 *
	 * @const
	 */
	const LANGUAGE = 'language';

	/**
	 * Default column used to store level.
	 *
	 * @const
	 * @since  1.4.0
	 */
	const LEVEL = 'level';

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
	 * Default column used to store parent identifier.
	 *
	 * @const
	 * @since  1.4.0
	 */
	const PARENT = 'parent_id';

	/**
	 * Default column used to store publish down date.
	 *
	 * @const
	 */
	const PUBLISH_DOWN = 'publish_down';

	/**
	 * Default column used to store publish up date.
	 *
	 * @const
	 */
	const PUBLISH_UP = 'publish_up';

	/**
	 * Default column used to store state.
	 *
	 * @const
	 */
	const STATE = 'published';
}
