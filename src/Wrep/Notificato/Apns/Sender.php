<?php

namespace Wrep\Notificato\Apns;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Sender implements LoggerAwareInterface
{
	private $gatewayFactory;
	private $gatewayPool;
	private $logger;

	/**
	 * Construct Sender
	 */
	public function __construct()
	{
		$this->setGatewayFactory(new GatewayFactory());
		$this->gatewayPool = array();
		$this->setLogger(new NullLogger());
	}

	/**
	 * Set the gateway factory to use for creating connections to the APNS gateway
	 *
	 * @param GatewayFactory The gateway factory to use
	 */
	public function setGatewayFactory(GatewayFactory $gatewayFactory)
	{
		$this->gatewayFactory = $gatewayFactory;
	}

	/**
	 * Get the current gateway factory
	 *
	 * @return GatewayFactory
	 */
	public function getGatewayFactory()
	{
		return $this->gatewayFactory;
	}

	/**
	 * Sets a logger instance on the object
	 *
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;

		// Also update the logger on all current gateways in our pool
		foreach ($this->gatewayPool as $gateway) {
			$gateway->setLogger($logger);
		}
	}

	/**
	 * Queues a message and flushes the gateway connection it must be send over immediately
	 *  Note: If you send multiple messages, queue as many as possible and flush them at once for maximum performance
	 *
	 * @param Message The message to send
	 * @return MessageEnvelope
	 */
	public function send(Message $message)
	{
		// Queue the message and flush the associated gateway
		$messageEnvelope = $this->queue($message);
		$this->flush( $message->getCertificate() );

		// Return the envelope
		return $messageEnvelope;
	}

	/**
	 * Queue a message on the correct APNS gateway connection
	 *
	 * @param Message The message to queue
	 * @param int The times Notificato should retry to deliver the message on failure (deprecated and ignored)
	 * @return MessageEnvelope
	 */
	public function queue(Message $message)
	{
		// Get the gateway for the certificate
		$gateway = $this->getGatewayForCertificate( $message->getCertificate() );

		// Queue the message
		return $gateway->queue($message);
	}

	/**
	 * Count of all queued messages
	 *
	 * @return int
	 */
	public function getQueueLength()
	{
		$queueLength = 0;

		foreach ($this->gatewayPool as $gateway)
		{
			$queueLength += $gateway->getQueueLength();
		}

		return $queueLength;
	}

	/**
	 * Send all queued messages
	 *
	 * @param Certificate|null When given only the gateway connection for the given certificate is flushed
	 */
	public function flush(Certificate $certificate = null)
	{
		// Check if we must flush a specific gateway
		if (null == $certificate)
		{
			// No, flush the whole gateway pool
			foreach ($this->gatewayPool as $gateway)
			{
				$gateway->flush();
			}
		}
		else
		{
			// Yes, flush only the requested gateway
			$this->getGatewayForCertificate($certificate)->flush();
		}
	}

	/**
	 * Get/create the gateway associated with the given certificate
	 *
	 * @param Certificate The certificate to get the gateway conenction for
	 * @return Gateway
	 */
	private function getGatewayForCertificate(Certificate $certificate)
	{
		// Get the fingerprint of the certificate
		$fingerprint = $certificate->getFingerprint();

		// If no gateway is available for this certificate create one
		if ( !isset($this->gatewayPool[$fingerprint]) )
		{
			$this->gatewayPool[$fingerprint] = $this->getGatewayFactory()->createGateway($certificate);
			$this->gatewayPool[$fingerprint]->setLogger($this->logger);
		}

		// Return the gateway connection for this certificate
		return $this->gatewayPool[$fingerprint];
	}
}