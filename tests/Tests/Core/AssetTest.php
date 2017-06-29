<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core;

use Phproberto\Joomla\Entity\Core\Asset;

/**
 * Article entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class AssetTest extends \TestCase
{
	/**
	 * instance loads an asset.
	 *
	 * @return  void
	 */
	public function testInstanceLoadsAnAsset()
	{
		$asset = Asset::instance(1);

		$this->assertEquals(1, $asset->getId());
	}
}
