<?php

namespace Wrep\Notificare\Apns;

class Sender
{
	private $gatewayFactory;
	private $gatewayPool;

	/**
	 * Construct Sender
	 */
	public function __construct()
	{
		$this->setGatewayFactory(new GatewayFactory());
		$this->gatewayPool = array();
	}

	/**
	 * Set the gateway factory to use for creating connections to the APNS gateway
	 *
	 * @param $gatewayFactory GatewayFactory The gateway factory to use
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
	 * Queues a message and flushes the gateway connection it must be send over immediately
	 *  Note: If you send multiple messages queue as many as possible and flush them at once for maximum performance
	 *
	 * @param $message Message The message to send
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
	 * Note: A certificate must be set in the message or as default to make this method work
	 *
	 * @param $message Message The message to queue
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
	 * @param $certificate Certificate|null When given only the gateway connection for the given certificate is flushed
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
	 * @param $certificate Certificate The certificate to get the gateway conenction for
	 * @return Gateway
	 */
	private function getGatewayForCertificate(Certificate $certificate)
	{
		// If no gateway is available for this certificate create one
		if ( !isset($this->gatewayPool[$certificate->getFingerprint()]) )
		{
			$this->gatewayPool[$certificate->getFingerprint()] = $this->getGatewayFactory()->createGateway($certificate);
		}

		// Return the gateway connection for this certificate
		return $this->gatewayPool[$certificate->getFingerprint()];
	}
}