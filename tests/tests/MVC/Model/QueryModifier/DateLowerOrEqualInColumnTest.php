<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\QueryModifier;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\MVC\Model\QueryModifier\DateLowerOrEqualInColumn;

/**
 * DateLowerOrEqualInColumn tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class DateLowerOrEqualInColumnTest extends \TestCase
{
	private $callbackExecuted = false;

	private $modifier;

	private $query;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->query = Factory::getDbo()->getQuery(true)
			->select('*')
			->from('#__test');

		$this->modifier = new DateLowerOrEqualInColumn($this->query, ['\'2018-02-27\''], 'a.test', [$this, 'queryCallback']);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function modifierIsApplied()
	{
		$this->assertSame(0, substr_count((string) $this->query, "WHERE `a`.`test` <= '2018-02-27'"));
		$this->modifier->apply();
		$this->assertSame(1, substr_count((string) $this->query, "WHERE `a`.`test` <= '2018-02-27'"));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function modifierIsNotAppliedIfNoValues()
	{
		$modifier = new DateLowerOrEqualInColumn($this->query, [], 'a.test', [$this, 'queryCallback']);
		$modifier->apply();

		$this->assertSame(0, substr_count((string) $this->query, "WHERE `a`.`test` <= '2018-02-27'"));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function callbackIsCalled()
	{
		$this->assertFalse($this->callbackExecuted);
		$this->modifier->apply();
		$this->assertTrue($this->callbackExecuted);
	}

	/**
	 * Query executed by the query.
	 *
	 * @return  void
	 */
	public function queryCallback()
	{
		$this->callbackExecuted = true;
	}
}
