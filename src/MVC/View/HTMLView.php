<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\View;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\View\HtmlView as BaseView;
use Phproberto\Joomla\Entity\Extensions\Entity\Component;
use Phproberto\Joomla\Entity\Extensions\Entity\Traits\HasComponent;
use Phproberto\Joomla\Entity\Traits;
use Phproberto\Joomla\Twig\Twig;

/**
 * Base HTML view.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class HTMLView extends BaseView
{
	use Traits\HasApp, HasComponent, Traits\HasLayoutData, Traits\HasMessages, Traits\HasRedirect;

	/**
	 * Allow to check access to the view in child classes.
	 *
	 * @param   string  $layout  Layout being rendered
	 *
	 * @return  boolean
	 */
	protected function allowLayout($layout = null)
	{
		return true;
	}

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return  string
	 */
	public function display($tpl = null)
	{
		if (!$this->allowLayout($tpl))
		{
			$app = $this->app();

			$this->enqueueMessagesInApp($app);

			return $app->redirect($this->redirectUrl());
		}

		return parent::display($tpl);
	}

	/**
	 * Try to guess component option from class.
	 *
	 * @return  string
	 */
	protected function componentOptionFromClass()
	{
		$class = $this->getClass();

		if (false !== strpos($class, '\\'))
		{
			$prefix = rtrim(strstr($class, 'View', true), '\\');

			$parts = explode("\\", $prefix);

			return array_key_exists(1, $parts) ? 'com_' . strtolower($parts[1]) : null;
		}

		return  'com_' . strtolower(strstr($class, 'View', true));
	}

	/**
	 * Retrieve active class name.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	protected function getClass()
	{
		return get_class($this);
	}

	/**
	 * Retrieve the active language.
	 *
	 * @return  Language
	 *
	 * @codeCoverageIgnore
	 */
	protected function language()
	{
		return Factory::getLanguage();
	}

	/**
	 * Load layout data.
	 *
	 * @return  self
	 */
	protected function loadLayoutData()
	{
		return [
			'view' => $this
		];
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 *
	 * @return  string  The output of the the template script.
	 *
	 * @throws  \Exception
	 */
	public function loadTemplate($tpl = null)
	{
		$layout = $this->getLayout();
		$tpl = $tpl ? $layout . '_' . $tpl : $layout;

		$renderer = Twig::instance();

		$data = $this->getLayoutData();
		$prefix = '@component/' . $this->component()->option() . '/' . $this->getName();

		$name = $prefix . '/' . $tpl . '.html.twig';

		if ($renderer->environment()->getLoader()->exists($name))
		{
			return $renderer->render($name, $data);
		}

		$name = $prefix . '/default.html.twig';

		return $renderer->render($name, $data);
	}

	/**
	 * Retrieve the title of this view.
	 *
	 * @return  string
	 */
	public function title()
	{
		$lang   = $this->language();
		$layout = $this->getLayout();
		$name   = $this->getName();
		$option = $this->component()->option();

		$langString = sprintf('%s_%s_%s_VIEW_TITLE', strtoupper($option), strtoupper($name), strtoupper($layout));

		if ($lang->hasKey($langString))
		{
			return Text::_($langString);
		}

		return Text::_(sprintf('%s_%s_VIEW_TITLE', strtoupper($option), strtoupper($name)));
	}
}
