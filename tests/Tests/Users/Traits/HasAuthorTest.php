<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tests\Users\Traits\Stubs\EntityWithAuthorAndEditor;

/**
 * HasAuthor trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasAuthorTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Name of the author column.
	 *
	 * @const
	 */
	const AUTHOR_COLUMN = 'created_by';

	/**
	 * author calls loadAuthor.
	 *
	 * @return  void
	 */
	public function testAuthorCallsLoadAuthor()
	{
		$author = new User(24);

		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('loadAuthor'))
			->getMock();

		$class->expects($this->once())
			->method('loadAuthor')
			->willReturn($author);

		$this->assertSame($author, $class->author());
	}

	/**
	 * author returns cached instance.
	 *
	 * @return  void
	 */
	public function testAuthorReturnsCachedInstance()
	{
		$author = new User(999);

		$class = new EntityWithAuthorAndEditor;

		$reflection = new \ReflectionClass($class);

		$authorProperty = $reflection->getProperty('author');
		$authorProperty->setAccessible(true);
		$authorProperty->setValue($class, $author);

		$this->assertSame($author, $class->author());
	}

	/**
	 * author reloads data.
	 *
	 * @return  void
	 */
	public function testAuthorReloadsData()
	{
		$author = new User(24);
		$reloadedAuthor = new User(999);

		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('loadAuthor'))
			->getMock();

		$class
			->method('loadAuthor')
			->will($this->onConsecutiveCalls($author, $reloadedAuthor));

		$this->assertSame($author, $class->author());
		$this->assertSame($reloadedAuthor, $class->author(true));
		$this->assertSame($reloadedAuthor, $class->author());
	}

	/**
	 * hasAuthor returns correct value.
	 *
	 * @return  void
	 */
	public function testHasAuthorReturnsCorrectValue()
	{
		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->expects($this->once())
			->method('columnAlias')
			->willReturn(static::AUTHOR_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999, static::AUTHOR_COLUMN => 22));

		$this->assertSame(true, $class->hasAuthor());
	}

	/**
	 * loadAuthor returns correct user.
	 *
	 * @return  void
	 */
	public function testLoadAuthorReturnsCorrectUser()
	{
		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->expects($this->once())
			->method('columnAlias')
			->willReturn(static::AUTHOR_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999, static::AUTHOR_COLUMN => 22));

		$method = $reflection->getMethod('loadAuthor');
		$method->setAccessible(true);

		$this->assertSame(User::instance(22), $method->invoke($class));
	}
}
