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
 * HasEditor trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasEditorTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Name of the primary key
	 *
	 * @const
	 */
	const EDITOR_COLUMN = 'modified_by';

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
	 * columnEditor returns correct value.
	 *
	 * @return  void
	 */
	public function testColumnAuthorReturnsCorrectValue()
	{
		$table = $this->getMockBuilder('MockedTable')
			->disableOriginalConstructor()
			->setMethods(array('getColumnAlias'))
			->getMock();

		$table->expects($this->once())
			->method('getColumnAlias')
			->willReturn(self::EDITOR_COLUMN);

		$class = $this->getMockBuilder(EntityWithAuthorAndEditor::class)
			->disableOriginalConstructor()
			->setMethods(array('table'))
			->getMock();

		$class->expects($this->once())
			->method('table')
			->willReturn($table);

		$reflection = new \ReflectionClass($class);

		$method = $reflection->getMethod('columnEditor');
		$method->setAccessible(true);

		$this->assertSame(self::EDITOR_COLUMN, $method->invoke($class));
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
			->setMethods(array('columnEditor'))
			->getMock();

		$class->expects($this->once())
			->method('columnEditor')
			->willReturn(self::EDITOR_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, self::EDITOR_COLUMN => 22));

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
			->setMethods(array('columnEditor'))
			->getMock();

		$class->expects($this->once())
			->method('columnEditor')
			->willReturn(self::EDITOR_COLUMN);

		$reflection = new \ReflectionClass($class);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($class, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($class, array('id' => 999, self::EDITOR_COLUMN => 22));

		$method = $reflection->getMethod('loadEditor');
		$method->setAccessible(true);

		$this->assertSame(User::instance(22), $method->invoke($class));
	}
}
