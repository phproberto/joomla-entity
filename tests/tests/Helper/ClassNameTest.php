<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Helper;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Helper\ClassName;

/**
 * ClassName tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ClassNameTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function namespaceReturnsExpectedValue()
	{
		$this->assertSame('Phproberto\Joomla\Entity\Tests\Helper', ClassName::namespace($this));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function parentNamespaceReturnsExpectedValue()
	{
		$this->assertSame('Phproberto\Joomla\Entity\Tests', ClassName::parentNamespace($this));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function withoutNamespaceReturnsExpectedValue()
	{
		$this->assertSame('ClassNameTest', ClassName::withoutNamespace($this));
	}
}
