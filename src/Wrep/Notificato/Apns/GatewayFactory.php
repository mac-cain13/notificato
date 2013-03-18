<?php

namespace Wrep\Notificato\Apns;

class GatewayFactory
{
	/**
	 * Create a Gateway object
	 *
	 * @param Certificate The certificate to use when connecting to APNS
	 */
	public function createGateway(Certificate $certificate)
	{
		return new Gateway($certificate);
	}
}