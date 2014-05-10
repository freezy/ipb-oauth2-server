<?php

namespace IPB;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'OAuth2' . DIRECTORY_SEPARATOR . 'Autoloader.php';
\OAuth2\Autoloader::register();

use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\AuthorizationCodeInterface;

class Storage implements AccessTokenInterface, ClientCredentialsInterface, AuthorizationCodeInterface {

	/**
	 * IPB's database interface
	 * @var interfaceDb
	 */
	private $db;

	public function __construct($db) {
		$this->db = $db;
	}

	/**
	 * Look up the supplied oauth_token from storage.
	 *
	 * We need to retrieve access token data as we create and verify tokens.
	 *
	 * @param string $oauth_token OAuth token to be check with.
	 * @return array
	 * An associative array as below, and return NULL if the supplied oauth_token
	 * is invalid:
	 * - expires: Stored expiration in unix timestamp.
	 * - client_id: (optional) Stored client identifier.
	 * - user_id: (optional) Stored user identifier.
	 * - scope: (optional) Stored scope values in space-separated string.
	 * - id_token: (optional) Stored id_token (if "use_openid_connect" is true).
	 *
	 * @ingroup oauth2_section_7
	 */
	public function getAccessToken($oauth_token) {
		$token = $this->db->buildAndFetch(array(
				'select' => '*',
				'from' => array('oauth_access_tokens' => 'token'),
				'where' => 'token.access_token = "' . $oauth_token . '"',
			)
		);
		if ($token) {
			// convert date string back to timestamp
			$token['expires'] = strtotime($token['expires']);
		}
		return $token;
	}

	/**
	 * Store the supplied access token values to storage.
	 *
	 * We need to store access token data as we create and verify tokens.
	 *
	 * @param string $oauth_token oauth_token to be stored.
	 * @param string $client_id Client identifier to be stored.
	 * @param string $user_id User identifier to be stored.
	 * @param int $expires Expiration to be stored as a Unix timestamp.
	 * @param string $scope (optional) Scopes to be stored in space-separated string.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null) {

		$expires = date('Y-m-d H:i:s', $expires);
		$token = compact('access_token', 'client_id', 'user_id', 'expires', 'scope');
		if ($this->getAccessToken($oauth_token)) {
			$res = $this->db->update('oauth_access_tokens', $token, 'access_token="' . $oauth_token . '"');
		} else {
			$res = $this->db->insert('oauth_access_tokens', $token);
		}
		return $res;
	}

	/**
	 * Fetch authorization code data (probably the most common grant type).
	 *
	 * Retrieve the stored data for the given authorization code.
	 *
	 * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
	 *
	 * @param string $c Authorization code to be check with.
	 * @return array An associative array as below, and NULL if the code is invalid
	 * @code
	 * return array(
	 *     "client_id"    => CLIENT_ID,      // REQUIRED Stored client identifier
	 *     "user_id"      => USER_ID,        // REQUIRED Stored user identifier
	 *     "expires"      => EXPIRES,        // REQUIRED Stored expiration in unix timestamp
	 *     "redirect_uri" => REDIRECT_URI,   // REQUIRED Stored redirect URI
	 *     "scope"        => SCOPE,          // OPTIONAL Stored scope values in space-separated string
	 * );
	 * @endcode
	 * @see http://tools.ietf.org/html/rfc6749#section-4.1
	 * @ingroup oauth2_section_4
	 */
	public function getAuthorizationCode($c) {
		$code = $this->db->buildAndFetch(array(
				'select' => '*',
				'from' => array('oauth_authorization_codes' => 'code'),
				'where' => 'code.authorization_code = "' . $c . '"'
			)
		);
		if ($code) {
			// convert date string back to timestamp
			$code['expires'] = strtotime($code['expires']);
		}
		return $code;
	}

	/**
	 * Take the provided authorization code values and store them somewhere.
	 *
	 * This function should be the storage counterpart to getAuthCode().
	 *
	 * If storage fails for some reason, we're not currently checking for
	 * any sort of success/failure, so you should bail out of the script
	 * and provide a descriptive fail message.
	 *
	 * Required for OAuth2::GRANT_TYPE_AUTH_CODE.
	 *
	 * @param string $c Authorization code to be stored.
	 * @param string $client_id Client identifier to be stored.
	 * @param string $user_id User identifier to be stored.
	 * @param string $redirect_uri Redirect URI(s) to be stored in a space-separated string.
	 * @param int $expires Expiration to be stored as a Unix timestamp.
	 * @param string $scope
	 * (optional) Scopes to be stored in space-separated string.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function setAuthorizationCode($c, $client_id, $user_id, $redirect_uri, $expires, $scope = null) {
		if (func_num_args() > 6) {
			// we are calling with an id token
			return call_user_func_array(array($this, 'setAuthorizationCodeWithIdToken'), func_get_args());
		}

		// convert expires to datestring
		$expires = date('Y-m-d H:i:s', $expires);
		$code = compact('c', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope');

		// if it exists, update it.
		if ($this->getAuthorizationCode($c)) {
			$res = $this->db->update('oauth_authorization_codes', $code, 'authorization_code="' . $code . '"');
		} else {
			$res = $this->db->insert('oauth_authorization_codes', $code);
		}
		return $res;
	}

	private function setAuthorizationCodeWithIdToken($c, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null) {
		// convert expires to datestring
		$expires = date('Y-m-d H:i:s', $expires);
		$code = compact('c', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope', 'id_token');

		// if it exists, update it.
		if ($this->getAuthorizationCode($c)) {
			$res = $this->db->update('oauth_authorization_codes', $code, 'authorization_code="' . $code . '"');
		} else {
			$res = $this->db->insert('oauth_authorization_codes', $code);
		}
		return $res;
	}

	/**
	 * Once an Authorization Code is used, it must be expired
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-4.1.2
	 *
	 *    The client MUST NOT use the authorization code
	 *    more than once.  If an authorization code is used more than
	 *    once, the authorization server MUST deny the request and SHOULD
	 *    revoke (when possible) all tokens previously issued based on
	 *    that authorization code
	 *
	 */
	public function expireAuthorizationCode($code) {
		$this->db->delete('oauth_authorization_codes', 'authorization_code="' . $code . '"');
	}

	/**
	 * Make sure that the client credentials is valid.
	 *
	 * @param string $client_id Client identifier to be check with.
	 * @param string $client_secret (optional) If a secret is required, check that they've given the right one.
	 * @return boolean TRUE if the client credentials are valid, and MUST return FALSE if it isn't.
	 * @see http://tools.ietf.org/html/rfc6749#section-3.1
	 * @ingroup oauth2_section_3
	 */
	public function checkClientCredentials($client_id, $client_secret = null) {

		$client = $this->db->buildAndFetch(array(
				'select' => '*',
				'from' => array('oauth_clients' => 'client'),
				'where' => 'client.client_id = "' . $client_id . '"'
			)
		);

		// make this extensible
		return $client && $client['client_secret'] == $client_secret;
	}

	/**
	 * Determine if the client is a "public" client, and therefore
	 * does not require passing credentials for certain grant types
	 *
	 * @param $client_id
	 * Client identifier to be check with.
	 *
	 * @return boolean TRUE if the client is public, and FALSE if it isn't.
	 *
	 * @see http://tools.ietf.org/html/rfc6749#section-2.3
	 * @see https://github.com/bshaffer/oauth2-server-php/issues/257
	 * @ingroup oauth2_section_2
	 */
	public function isPublicClient($client_id) {

		$client = $this->db->buildAndFetch(array(
				'select' => '*',
				'from' => array('oauth_clients' => 'client'),
				'where' => 'client.client_id = "' . $client_id . '"'
			)
		);

		if (!$client) {
			return false;
		}
		return empty($client['client_secret']);
	}

	/**
	 * Get client details corresponding client_id.
	 *
	 * OAuth says we should store request URIs for each registered client.
	 * Implement this function to grab the stored URI for a given client id.
	 *
	 * @param string $client_id Client identifier to be check with.
	 *
	 * @return array Client details. The only mandatory key in the array is "redirect_uri".
	 *     This function MUST return FALSE if the given client does not exist or is
	 *      invalid. "redirect_uri" can be space-delimited to allow for multiple valid uris.
	 * @code
	 * return array(
	 *     "redirect_uri" => REDIRECT_URI,      // REQUIRED redirect_uri registered for the client
	 *     "client_id"    => CLIENT_ID,         // OPTIONAL the client id
	 *     "grant_types"  => GRANT_TYPES,       // OPTIONAL an array of restricted grant types
	 *     "user_id"      => USER_ID,           // OPTIONAL the user identifier associated with this client
	 *     "scope"        => SCOPE,             // OPTIONAL the scopes allowed for this client
	 * );
	 * @endcode
	 * @ingroup oauth2_section_4
	 */
	public function getClientDetails($client_id) {
		return $this->db->buildAndFetch(array(
				'select' => '*',
				'from' => array('oauth_clients' => 'client'),
				'where' => 'client.client_id = "' . $client_id . '"'
			)
		);
	}

	/**
	 * Get the scope associated with this client
	 *
	 * @param string $client_id client ID
	 * @return string the space-delineated scope list for the specified client_id
	 */
	public function getClientScope($client_id) {
		if (!$clientDetails = $this->getClientDetails($client_id)) {
			return false;
		}
		if (isset($clientDetails['scope'])) {
			return $clientDetails['scope'];
		}
		return null;
	}

	/**
	 * Check restricted grant types of corresponding client identifier.
	 *
	 * If you want to restrict clients to certain grant types, override this
	 * function.
	 *
	 * @param string $client_id Client identifier to be check with.
	 * @param string $grant_type Grant type to be check with
	 *
	 * @return boolean TRUE if the grant type is supported by this client identifier, and
	 *                 FALSE if it isn't.
	 *
	 * @ingroup oauth2_section_4
	 */
	public function checkRestrictedGrantType($client_id, $grant_type) {
		$details = $this->getClientDetails($client_id);
		if (isset($details['grant_types'])) {
			$grant_types = explode(' ', $details['grant_types']);

			return in_array($grant_type, (array) $grant_types);
		}
		// if grant_types are not defined, then none are restricted
		return true;
	}
}