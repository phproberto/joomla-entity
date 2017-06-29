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
	 * getTable returns correct instance.
	 *
	 * @return  void
	 */
	public function testGetTableReturnsCorrectInstance()
	{
		$asset = new Asset;

		$this->assertInstanceOf('JTableAsset', $asset->getTable());
	}
}
