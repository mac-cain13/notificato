<?php

namespace Wrep\Notificare\Test\Apns\Mock;

use Wrep\Notificare\Apns\Certificate;
use Wrep\Notificare\Apns\GatewayFactory;

class MockGatewayFactory extends GatewayFactory
{
	/**
	 * Create a Gateway object
	 *
	 * @param $certificate Certificate The certificate to use when connecting to APNS
	 */
	public function createGateway(Certificate $certificate)
	{
		return new MockGateway($certificate);
	}
}