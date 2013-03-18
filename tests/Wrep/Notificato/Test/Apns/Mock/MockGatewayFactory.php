<?php

namespace Wrep\Notificato\Test\Apns\Mock;

use Wrep\Notificato\Apns\Certificate;
use Wrep\Notificato\Apns\GatewayFactory;

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