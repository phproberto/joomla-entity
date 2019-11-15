<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Searcher;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Searcher\BaseSearcher;
use Phproberto\Joomla\Entity\Tests\Searcher\Stubs\Searcher;

/**
 * Category searcher tests.
 *
 * @since   1.4.0
 */
class BaseSearcherTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsOptions()
	{
		$searcher = $this->getMockForAbstractClass(
			BaseSearcher::class,
			[
				['test' => 'my-value']
			]
		);

		$this->assertSame('my-value', $searcher->options()->get('test'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorAppliesDefaultOptions()
	{
		$searcher = new Searcher(['option' => 'overriden-value', 'custom' => 'custom-value']);

		$this->assertSame('overriden-value', $searcher->options()->get('option'));
		$this->assertSame('another-default-value', $searcher->options()->get('another-option'));
		$this->assertSame('custom-value', $searcher->options()->get('custom'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsInitialisedSearcher()
	{
		$searcher = Searcher::instance(['my-option' => 'my-value']);

		$this->assertSame('another-default-value', $searcher->options()->get('another-option'));
		$this->assertSame('my-value', $searcher->options()->get('my-option'));
	}
}
