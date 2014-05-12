<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Server.php';

class public_oauth2_server_token extends ipsCommand {

	public function doExecute(ipsRegistry $registry) {

		// setup storage and server
		$storage = new IPB\Storage($this->DB, $this->memberData['member_id']);
		$server = IPB\Server::createServer($storage);

		$request = OAuth2\Request::createFromGlobals();

		// handle a request for an OAuth2.0 Access Token and send the response to the client
		$server->handleTokenRequest($request)->send();
	}
}