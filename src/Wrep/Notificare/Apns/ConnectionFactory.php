<?php

namespace Wrep\Notificare\Apns;

class ConnectionFactory
{
	/**
	 * Create a Connect object
	 *
	 * @param $certificate Certificate The certificate to use when connecting to APNS
	 */
	public function createConnection(Certificate $certificate)
	{
		return new Connection($certificate);
	}
}