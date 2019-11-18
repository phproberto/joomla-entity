<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplication;

/**
 * For classes sending emails.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasMailer
{
	/**
	 * Get the list of users that receive system messages.
	 *
	 * @var  array
	 */
	protected $systemMessageRecipients;

	/**
	 * Get the email mailer.
	 *
	 * @return  JMail
	 */
	protected function getMailer()
	{
		$config = Factory::getConfig();

		$mailer = Factory::getMailer();
		$mailer->isHtml(true);
		$mailer->{'Encoding'} = 'base64';
		$mailer->setSender([$config->get('mailfrom'), $config->get('fromname')]);

		return $mailer;
	}

	/**
	 * Get the message table.
	 *
	 * @return  \MessagesTableMessage
	 */
	protected function getMessageTable()
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_messages/tables');

		return Table::getInstance('Message', 'MessagesTable');
	}

	/**
	 * Get an array with the information of the users that receive system messages.
	 *
	 * @return  array
	 */
	protected function getSystemMessagesRecipients()
	{
		if (null === $this->systemMessageRecipients)
		{
			$this->systemMessageRecipients = $this->loadSystemMessageRecipients();
		}

		return $this->systemMessageRecipients;
	}

	/**
	 * Load system message recipients from database.
	 *
	 * @return  array
	 */
	protected function loadSystemMessageRecipients()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('block') . ' = ' . (int) 0)
			->where($db->quoteName('sendEmail') . ' = ' . (int) 1);
		$db->setQuery($query);

		return $db->loadObjectList() ?: [];
	}

	/**
	 * Notify email failures.
	 *
	 * @param   \JMail  $mailer  Joomla Mailer
	 *
	 * @return  void
	 */
	protected function notifyEmailFailure($mailer)
	{
		$systemMessageRecipients = $this->getSystemMessagesRecipients();

		if (!$systemMessageRecipients)
		{
			return;
		}

		$subject = Text::sprintf('LIB_SEC_EMAIL_ERROR_SENDING', $mailer->{'Subject'});
		$messageTable = $this->getMessageTable();

		$date = new Date;

		foreach ($systemMessageRecipients as $systemMessageRecipient)
		{
			$message = [
				'message_id'   => 0,
				'user_id_from' => $systemMessageRecipient->id,
				'user_id_to'   => $systemMessageRecipient->id,
				'date_time'    => $date->toSql(),
				'subject'      => $subject,
				'message'      => $mailer->{'ErrorInfo'}
			];

			if (!$messageTable->save($message))
			{
				return;
			}

			$messageTable->reset();
		}
	}

	/**
	 * Send an email.
	 *
	 * @param   array   $recipients  Email recipient
	 * @param   string  $subject     Email subject
	 * @param   string  $body        Email body
	 * @param   array   $options     Optional array of options
	 *
	 * @return  boolean
	 */
	protected function sendMail(array $recipients, $subject, $body, $options = ['notifyErrorMessages' => true])
	{
		$mailer = $this->getMailer();

		foreach ($recipients as $recipient)
		{
			$mailer->addRecipient($recipient);
		}

		$mailer->setSubject($subject);
		$mailer->setBody($body);

		$replyTo = isset($options['replyTo']) ? array_filter((array) $options['replyTo']) : [];

		foreach ($replyTo as $replyToAddress)
		{
			$mailer->addReplyTo($replyToAddress);
		}

		$result = $mailer->send();
		$notifyErrorMessages = isset($options['notifyErrorMessages']) ? $options['notifyErrorMessages'] : true;

		if ($notifyErrorMessages && $mailer->isError())
		{
			$this->notifyEmailFailure($mailer);
		}

		return ($result instanceof \Exception) ? false : (bool) $result;
	}
}
