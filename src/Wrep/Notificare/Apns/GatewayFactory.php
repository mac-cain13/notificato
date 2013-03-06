<?php

namespace Wrep\Notificare\Apns;

class GatewayFactory
{
	/**
	 * Create a Gateway object
	 *
	 * @param $certificate Certificate The certificate to use when connecting to APNS
	 */
	public function createConnection(Certificate $certificate)
	{
		return new Gateway($certificate);
	}
}