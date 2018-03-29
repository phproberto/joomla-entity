<?php
/**
 * @package     Phproberto\Joomla\Entity
 * @subpackage  Library
 *
 * @copyright   Copyright (C) 2018 Roberto Segura López. All rights reserved.
 * @license     GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;

$composerAutoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($composerAutoload))
{
	require_once $composerAutoload;
}

// Load library language
$lang = Factory::getLanguage();
$lang->load('lib_joomla_entity', __DIR__);
