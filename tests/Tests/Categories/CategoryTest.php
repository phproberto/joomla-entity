<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Categories;

use Phproberto\Joomla\Entity\Categories\Category;

/**
 * Category entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CategoryTest extends \TestCaseDatabase
{
	/**
	 * Asset can be retrieved.
	 *
	 * @return  void
	 */
	public function testAssetCanBeRetrieved()
	{
		$category = new Category;
		$reflection = new \ReflectionClass($category);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($category, array('id' => 999));

		$asset = $category->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(0, $asset->id());

		$category = new Category;

		$rowProperty->setValue($category, array('id' => 999, 'asset_id' => 666));

		$asset = $category->getAsset();

		$this->assertInstanceOf('Phproberto\Joomla\Entity\Core\Asset', $asset);
		$this->assertSame(666, $asset->id());
	}
}
