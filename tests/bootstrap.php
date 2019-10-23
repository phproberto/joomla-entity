<?php
/**
 * Joomla! component.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

require_once JPATH_BASE . '/tests/unit/bootstrap.php';

if (!defined('JPATH_TESTS_PHPROBERTO'))
{
	define('JPATH_TESTS_PHPROBERTO', realpath(__DIR__));
}

define('DEFAULT_TOKEN_FOR_URLS', 'cfcd208495d565ef66e7dff9f98764da');

require_once dirname(__FILE__) . '/../vendor/autoload.php';

