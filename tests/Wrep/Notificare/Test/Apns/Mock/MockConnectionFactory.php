<?php

namespace Wrep\Notificare\Test\Apns\Mock;

use Wrep\Notificare\Apns\Certificate;
use Wrep\Notificare\Apns\ConnectionFactory;

class MockConnectionFactory extends ConnectionFactory
{
	/**
	 * Create a Connect object
	 *
	 * @param $certificate Certificate The certificate to use when connecting to APNS
	 */
	public function createConnection(Certificate $certificate)
	{
		return new MockConnection($certificate);
	}
}