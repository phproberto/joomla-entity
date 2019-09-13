<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State;

defined('_JEXEC') || die;

use PHPUnit\Framework\TestCase;
use Phproberto\Joomla\Entity\MVC\Model\State\Property;

/**
 * Property tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class PropertyTest extends TestCase
{
	/**
	 * @test
	 *
	 * @expectedException \RuntimeException
	 *
	 * @return void
	 */
	public function constructorThrowsExceptionForMissingKey()
	{
		$property = new Property('   ');
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function keyCanBeRetrieved()
	{
		$property = new Property('my.key');

		$this->assertSame('my.key', $property->key());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function isPopulableReturnsCorrectValue()
	{
		$property = new Property('my.key');

		$this->assertFalse($property->isPopulable());

		$reflection = new \ReflectionClass($property);
		$isPopulableProperty = $reflection->getProperty('isPopulable');
		$isPopulableProperty->setAccessible(true);

		$isPopulableProperty->setValue($property, true);

		$this->assertTrue($property->isPopulable());
	}
}
