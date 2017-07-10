<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags\Traits;

use Phproberto\Joomla\Entity\EntityCollection;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Tests\Tags\Traits\Stubs\ClassWithTags;

/**
 * HasTags trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasTagsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getTags returns correct data.
	 *
	 * @return  void
	 */
	public function testGetTagsReturnsCorrectData()
	{
		$entity = new ClassWithTags;

		$this->assertEquals(new EntityCollection, $entity->getTags());

		$entity->tagsIds = array(999);

		// Previous data with no reload
		$this->assertEquals(new EntityCollection, $entity->getTags());
		$this->assertEquals(new EntityCollection(array(new Tag(999))), $entity->getTags(true));
	}
}
