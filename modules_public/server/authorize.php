<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Server.php';

class public_oauth2_server_authorize extends ipsCommand {

	public function doExecute(ipsRegistry $registry) {

		$member_id = $this->memberData['member_id'];

		// enforce login
		if (!$member_id) {
			$this->registry->output->silentRedirect($this->settings['base_url'] . 'app=core&module=global&section=login&referer=' . urlencode('?' . $this->settings['query_string_real']));
			return;
		}

		// setup storage and server
		$storage = new IPB\Storage($this->DB, $member_id);
		$server = IPB\Server::createServer($storage);

		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();

		// validate the authorize request
		if (!$server->validateAuthorizeRequest($request, $response)) {
			$response->send();
			die;
		}

		// check for saved authorizations
		if ($storage->hasAuthorization($this->request['client_id'], $member_id, '')) {
			$server->handleAuthorizeRequest($request, $response, true, $member_id);
			$response->send();
			die;
		}

		// display an authorization form
		$this->lang->loadLanguageFile(array('public_lang'), 'oauth2');
		if (empty($_POST)) {
			$client = $storage->getClientDetails($this->request['client_id']);
			$this->registry->output->setTitle("Authorize Application");
			$this->registry->output->addNavigation("Authorize Application", NULL);
			$this->registry->output->addContent($this->registry->output->getTemplate('oauth2')->authorize($client));
			$this->registry->output->sendOutput();
			die;
		}

		// print the authorization code if the user has authorized your client
		$is_authorized = ($_POST['authorized'] === $this->lang->words['o_authorize_application']);
		if ($is_authorized) {
			$storage->setAuthorization($this->request['client_id'], $member_id, '');
		}
		$server->handleAuthorizeRequest($request, $response, $is_authorized, $member_id);
		$response->send();
	}
}