<?php

namespace Wrep\Notificato;

use Wrep\Notificato\Apns as Apns;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Notificato implements LoggerAwareInterface
{
	private $messageFactory;
	private $sender;
	private $logger;

	/**
	 * Notificato constructor
	 *
	 * @param string|null Path to the PEM certificate file to use as default certificate, null for no default certificate
	 * @param string|null Passphrase to use with the PEM file
	 * @param boolean Set to false to skip the validation of the certificate, default true
	 * @param string|null APNS environment this certificate is valid for, by default autodetects during validation
	 */
	public function __construct($pemFile = null, $passphrase = null, $validate = true, $endpointEnv = null)
	{
		$defaultCertificate = null;
		if (null !== $pemFile) {
			$defaultCertificate = $this->createCertificate($pemFile, $passphrase, $validate, $endpointEnv);
		}

		$this->setLogger(new NullLogger());
		$this->setMessageFactory(new Apns\MessageFactory($defaultCertificate));
		$this->setSender(new Apns\Sender());
	}

	/**
     * Sets a logger instance on the object
     *
     * @param Psr\Log\LoggerInterface $logger
     */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;

		// Also update the logger of the sender
		if ($this->sender instanceOf LoggerAwareInterface) {
			$this->sender->setLogger($logger);
		}
	}

	/**
     * Sets the sender to use
     *
     * @param Apns\Sender $sender
     */
	public function setSender(Apns\Sender $sender)
	{
		$this->sender = $sender;
		$this->sender->setLogger($this->logger);
	}

	/**
     * Sets the message factory to use
     *
     * @param Apns\MessageFactory $messageFactory
     */
	public function setMessageFactory(Apns\MessageFactory $messageFactory)
	{
		$this->messageFactory = $messageFactory;
	}

	/**
	 * Create an APNS Certificate
	 *
	 * @param string Path to the PEM certificate file
	 * @param string|null Passphrase to use with the PEM file
	 * @param boolean Set to false to skip the validation of the certificate, default true
	 * @param string|null APNS environment this certificate is valid for, by default autodetects during validation
	 * @return Apns\Certificate
	 */
	public function createCertificate($pemFile, $passphrase = null, $validate = true, $endpointEnv = null)
	{
		return new Apns\Certificate($pemFile, $passphrase, $validate, $endpointEnv);
	}

	/**
	 * Create a Message
	 *
	 * @param string Receiver of this message
	 * @param Apns\Certificate|null The certificate that must be used for the APNS connection this message is send over, null to use the default certificate
	 * @return Apns\Message
	 */
	public function createMessage($deviceToken, Apns\Certificate $certificate = null)
	{
		return $this->messageFactory->createMessage($deviceToken, $certificate);
	}

	/**
	 * Queue a message on the correct APNS gateway connection
	 *
	 * @param Apns\Message The message to queue
	 * @param int The times Notificato should retry to deliver the message on failure
	 * @return Apns\MessageEnvelope
	 */
	public function queue(Apns\Message $message, $retryLimit = Apns\MessageEnvelope::DEFAULT_RETRY_LIMIT)
	{
		return $this->sender->queue($message, $retryLimit);
	}

	/**
	 * Send all queued messages
	 *
	 * @param Apns\Certificate|null When given only the gateway connection for the given certificate is flushed
	 */
	public function flush(Apns\Certificate $certificate = null)
	{
		$this->sender->flush($certificate);
	}

	/**
	 * Queues a message and flushes the gateway connection it must be send over immediately
	 *  Note: If you send multiple messages, queue as many as possible and flush them at once for maximum performance
	 *
	 * @param Apns\Message The message to send
	 * @return Apns\MessageEnvelope
	 */
	public function send(Apns\Message $message)
	{
		return $this->sender->send($message);
	}

	/**
	 * Receive the feedback tuples from APNS
	 *
	 * @param Apns\Certificate|null The certificate to use to connect to APNS, default use the default certificate
	 * @return array Array containing FeedbackTuples received from Apple
	 */
	public function receiveFeedback(Apns\Certificate $certificate = null)
	{
		if (null == $certificate) {
			$certificate = $this->messageFactory->getDefaultCertificate();
		}

		$feedback = new Apns\Feedback\Feedback($certificate);
		$feedback->setLogger($this->logger);
		return $feedback->receive();
	}
}