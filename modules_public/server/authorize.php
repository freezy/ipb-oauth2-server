<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';

class public_oauth2_server_authorize extends ipsCommand {


	public function doExecute(ipsRegistry $registry) {


		// enforce login
		if (!$this->memberData['member_id']) {
			$this->registry->output->silentRedirect($this->settings['base_url'] . 'app=core&module=global&section=login&referer=' . urlencode('?' . $this->settings['query_string_real']));
			return;
		}

		// setup storage
		$storage = new IPB\Storage($this->DB);

		// setup server
		$server = new OAuth2\Server($storage);

		// add the "client credentials" grant type (it is the simplest of the grant types)
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

		// add the "authorization code" grant type (this is where the oauth magic happens)
		$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();

		// validate the authorize request
		if (!$server->validateAuthorizeRequest($request, $response)) {
			$response->send();
			die;
		}

		// display an authorization form
		if (empty($_POST)) {
			exit('
<form method="post">
  <label>Do You Authorize TestClient?</label><br />
  <input type="submit" name="authorized" value="yes">
  <input type="submit" name="authorized" value="no">
</form>');
		}

		// print the authorization code if the user has authorized your client
		$is_authorized = ($_POST['authorized'] === 'yes');
		$server->handleAuthorizeRequest($request, $response, $is_authorized);
		if ($is_authorized) {
			// this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
			$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
			exit("SUCCESS! Authorization Code: $code");
		}
		$response->send();
	}
}