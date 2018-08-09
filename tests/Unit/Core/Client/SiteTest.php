<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Client;

use Phproberto\Joomla\Entity\Core\Client\Site;

/**
 * Tests for Site client.
 *
 * @since  1.1.0
 */
class SiteTest extends \TestCase
{
	/**
	 * Test getFolder returns the correct folder.
	 *
	 * @return  void
	 */
	public function testGetFolderReturnsCorrectFolder()
	{
		$client = new Site;
		$this->assertEquals(JPATH_SITE, $client->getFolder());
	}

	/**
	 * Test getId returns the correct id.
	 *
	 * @return  void
	 */
	public function testGetIdReturnsCorrectId()
	{
		$client = new Site;
		$this->assertEquals(Site::ID, $client->getId());
	}

	/**
	 * Test getName returns correct name.
	 *
	 * @return  void
	 */
	public function testGetNameRetursCorrectName()
	{
		$client = new Site;
		$this->assertEquals(Site::NAME, $client->getName());
	}

	/**
	 * Test isAdmin returns false.
	 *
	 * @return  void
	 */
	public function testIsAdminReturnsFalse()
	{
		$client = new Site;
		$this->assertFalse($client->IsAdmin());
	}

	/**
	 * Test isSite returns true.
	 *
	 * @return  void
	 */
	public function testIsSiteReturnsTrue()
	{
		$client = new Site;
		$this->assertTrue($client->IsSite());
	}
}
