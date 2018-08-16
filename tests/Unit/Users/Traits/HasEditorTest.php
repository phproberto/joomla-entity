<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tests\Unit\Users\Traits\Stubs\EntityWithAuthorAndEditor;

/**
 * HasEditor trait tests.
 *
 * @since   1.1.0
 */
class HasEditorTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Name of the editor column.
	 *
	 * @const
	 */
	const EDITOR_COLUMN = 'modified_by';

	/**
	 * @test
	 *
	 * @return void
	 */
	public function editorIdReturnsZeroForMissingEditorColumn()
	{
		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->method('columnAlias')
			->willReturn(static::EDITOR_COLUMN);

		$this->assertSame(0, $class->editorId());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function editorIdReturnsCorrectId()
	{
		$entity = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::EDITOR_COLUMN);

		$entity->bind(
			[
				'id' => 99,
				static::EDITOR_COLUMN => 34
			]
		);

		$this->assertSame(34, $entity->editorId());

		$entity->bind(
			[
				'id' => 99,
				static::EDITOR_COLUMN => 333
			]
		);

		$this->assertSame(333, $entity->editorId());
	}

	/**
	 * editor calls loadEditor.
	 *
	 * @return  void
	 */
	public function testEditorCallsLoadEditor()
	{
		$editor = new User(24);

		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('loadEditor'))
			->getMock();

		$class->expects($this->once())
			->method('loadEditor')
			->willReturn($editor);

		$this->assertSame($editor, $class->editor());
	}

	/**
	 * editor returns cached instance.
	 *
	 * @return  void
	 */
	public function testAuthorReturnsCachedInstance()
	{
		$editor = new User(999);

		$class = new EntityWithAuthorAndEditor;

		$reflection = new \ReflectionClass($class);

		$editorProperty = $reflection->getProperty('editor');
		$editorProperty->setAccessible(true);
		$editorProperty->setValue($class, $editor);

		$this->assertSame($editor, $class->editor());
	}

	/**
	 * editor reloads data.
	 *
	 * @return  void
	 */
	public function testEditorReloadsData()
	{
		$editor = new User(24);
		$reloadedEditor = new User(999);

		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('loadEditor'))
			->getMock();

		$class->expects($this->at(0))
			->method('loadEditor')
			->willReturn($editor);

		$class->expects($this->at(1))
			->method('loadEditor')
			->willReturn($reloadedEditor);

		$this->assertSame($editor, $class->editor());
		$this->assertSame($reloadedEditor, $class->editor(true));
		$this->assertSame($reloadedEditor, $class->editor());
	}

	/**
	 * hasEditor returns correct value.
	 *
	 * @return  void
	 */
	public function testHasEditorReturnsCorrectValue()
	{
		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->method('columnAlias')
			->willReturn(static::EDITOR_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, static::EDITOR_COLUMN => 22));

		$this->assertSame(true, $class->hasEditor());
	}

	/**
	 * loadEditor returns correct user.
	 *
	 * @return  void
	 */
	public function testLoadEditorReturnsCorrectUser()
	{
		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('columnAlias'))
			->getMock();

		$class->expects($this->once())
			->method('columnAlias')
			->willReturn(static::EDITOR_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999, static::EDITOR_COLUMN => 22));

		$method = $reflection->getMethod('loadEditor');
		$method->setAccessible(true);

		$this->assertSame(User::find(22), $method->invoke($class));
	}
}
