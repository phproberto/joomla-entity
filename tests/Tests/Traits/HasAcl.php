<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\EntityWithAcl;

/**
 * HasAcl trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAclTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * aclPrefix returns correct value.
	 *
	 * @return  void
	 */
	public function testAclPrefixReturnsCorrectValue()
	{
		$entity = new EntityWithAcl;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('aclPrefix');
		$method->setAccessible(true);

		$this->assertSame('core', $method->invoke($entity));
	}

	/**
	 * assetName returns component asset name for no id.
	 *
	 * @return  void
	 */
	public function testAssetNameReturnsComponentAssetForNoId()
	{
		$entity = new EntityWithAcl;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('assetName');
		$method->setAccessible(true);

		$this->assertSame('com_tests', $method->invoke($entity));
	}

	/**
	 * assetName returns correct asset name with id.
	 *
	 * @return  void
	 */
	public function testAssetNameReturnsCorrectAssetNameWithId()
	{
		$entity = new EntityWithAcl(34);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('assetName');
		$method->setAccessible(true);

		$this->assertSame('com_tests.entitywithacl.34', $method->invoke($entity));
	}
}
