<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithLink;

/**
 * HasLink trait tests.
 *
 * @since   1.1.0
 */
class HasLinkTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getLink returns correct value.
	 *
	 * @return  void
	 */
	public function testGetLinkReturnsCorrectValue()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$entity = new EntityWithLink;
		$this->assertSame(null, $entity->link());

		$entity = new EntityWithLink(999);

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999]);

		$this->assertSame('/999', $entity->link());

		$rowProperty->setValue($entity, ['id' => 999, 'alias' => 'sample-alias']);

		// Without reload returns old link
		$this->assertSame('/999', $entity->link());
		$this->assertSame('/999:sample-alias', $entity->link(true));
	}
}
