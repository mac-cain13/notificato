<?php

namespace Wrep\Notificato;

use Wrep\Notificato\Apns as Apns;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Notificato implements LoggerAwareInterface
{
	private $sender;
	private $logger;

	private $certificateFactory;
	private $feedbackFactory;
	private $messageBuilder;

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
		$this->setLogger( new NullLogger() );
		$this->setSender( new Apns\Sender() );

		$this->setCertificateFactory( new Apns\CertificateFactory($pemFile, $passphrase, $validate, $endpointEnv) );
		$this->setFeedbackFactory( new Apns\Feedback\FeedbackFactory($this->certificateFactory) );
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
		return $this->certificateFactory->createCertificate($pemFile, $passphrase, $validate, $endpointEnv);
	}

	/**
	 * Create a Message builder
	 *
	 * @return Apns\MessageBuilder
	 */
	public function messageBuilder()
	{
		$builder = Apns\Message::builder();

		if ($this->certificateFactory->getDefaultCertificate() != null) {
			$builder->setCertificate( $this->certificateFactory->getDefaultCertificate() );
		}

		return $builder;
	}

	/**
	 * Queue a message on the correct APNS gateway connection
	 *
	 * @param Apns\Message The message to queue
	 * @return Apns\MessageEnvelope
	 */
	public function queue(Apns\Message $message)
	{
		return $this->sender->queue($message);
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
		$feedback = $this->feedbackFactory->createFeedback($certificate);
		$feedback->setLogger($this->logger);

		return $feedback->receive();
	}

	/**
	 * Sets the sender to use.
	 * Note: The given sender will get the logger used by this Notificato object
	 *
	 * @param Apns\Sender $sender
	 */
	public function setSender(Apns\Sender $sender)
	{
		$this->sender = $sender;
		$this->sender->setLogger($this->logger);
	}

	/**
	 * Sets a logger instance on the object.
	 * Note: The sender is automaticly updated with this logger
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
	 * Sets the certificate factory to use.
	 * Note: If you set a certificate factory you are responsible for setting the correct default certificate.
	 * Note: The FeedbackFactory and MessageFactory are automaticly updated with the given CertificateFactory.
	 *
	 * @param Apns\CertificateFactory $messageFactory
	 */
	public function setCertificateFactory(Apns\CertificateFactory $certificateFactory)
	{
		$this->certificateFactory = $certificateFactory;

		// Also update the certificate factory of the feedback factory
		if (null !== $this->feedbackFactory) {
			$this->feedbackFactory->setCertificateFactory($this->certificateFactory);
		}
	}

	/**
	 * Sets the feedback factory to use.
	 * Note: The certificate factory is automaticly set to the factory used by this Notificato object
	 *
	 * @param Apns\FeedbackFactory $feedbackFactory
	 */
	public function setFeedbackFactory(Apns\Feedback\FeedbackFactory $feedbackFactory)
	{
		$this->feedbackFactory = $feedbackFactory;
		$this->feedbackFactory->setCertificateFactory($this->certificateFactory);
	}
}
