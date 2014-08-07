<?php

namespace IPB;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'OAuth2' . DIRECTORY_SEPARATOR . 'Autoloader.php';
\OAuth2\Autoloader::register();

use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\RefreshToken;

class Server {

	/**
	 * Creates an OAuth2 server with the given storage.
	 *
	 * @param Storage $storage
	 * @return \OAuth2\Server
	 */
	public static function createServer(Storage $storage) {

		// setup server
		$server = new \OAuth2\Server($storage, array('enforce_state' => false));

		// add the "client credentials" grant type (it is the simplest of the grant types)
		$server->addGrantType(new ClientCredentials($storage));

		// add the "authorization code" grant type (this is where the oauth magic happens)
		$server->addGrantType(new AuthorizationCode($storage));

		// create the grant type
		//$server->addGrantType(new RefreshToken($storage));

		return $server;
	}
}
