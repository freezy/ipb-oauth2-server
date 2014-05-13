# OAuth2 Server for IP.Board

This adds an OAuth2 server to your [IP.Board](http://www.invisionpower.com/apps/board/). This allows
external applications to request authorization to private user details of IP.Board members without
getting their password.

Developers accessing this API need to have their application registered via IP.Board's admin control
panel. This can be done by navigating through ``Other Apps`` -> ``OAuth2 Server``. There you can list,
create, update and delete your applications.

## Web Application Flow

In all examples, we assume that your board is running at ``https://ipboard/index.php``, of course you
will need to adapt the host and path to your site.

### 1. Redirect users to request IP.Board access

	GET https://ipboard/index.php?app=oauth2&module=server&section=authorize

### Parameters

Name | Type | Description
-----|------|--------------
`client_id`|`string` | **Required**. The client ID generated when creating the application in AdminCP.
`redirect_uri`|`string` | **Required**. The URL in your app where users will be sent after authorization.
`state`|`string` | **Required**. An unguessable random string. It is used to protect against cross-site request forgery attacks.

### 2. IP.Board redirects back to your site

If the user accepts your request, GitHub redirects back to your site
with a temporary code in a `code` parameter as well as the state you provided in
the previous step in a `state` parameter. If the states don't match, the request
has been created by a third party and the process should be aborted.

Exchange this for an access token:

	POST https://ipboard/index.php?app=oauth2&module=server&section=token

### Parameters

Name | Type | Description
-----|------|---------------
`client_id`|`string` | **Required**. The client ID generated when creating the application in AdminCP.
`client_secret`|`string` | **Required**. The client secret generated when creating the application in AdminCP.
`code`|`string` | **Required**. The code you received as a response to [Step 1](#redirect-users-to-request-ip.board-access).
`redirect_uri`|`string` | The URL in your app where users will be sent after authorization.

### Response

The response will take the following form:

    access_token=e72e16c7e42f292c6912e7710c838347ae178b4a&token_type=bearer

### 3. Use the access token to access the API

The access token allows you to retrieve the user's profile data. You can do this
by requesting this URL:

    GET https://ipboard/index.php?app=oauth2&module=server&section=profile&access_token=...


You can pass the token in the query params like shown above, but a
cleaner approach is to include it in the Authorization header

    Authorization: Bearer OAUTH-TOKEN

For example, in curl you can set the Authorization header like this:

    curl -H "Authorization: Bearer OAUTH-TOKEN" https://ipboard/index.php?app=oauth2&module=server&section=profile

## Credits

	* [OAuth 2.0 Server for PHP](http://bshaffer.github.io/oauth2-server-php-docs/), an excellent
	  OAuth2 library in PHP
	* [IPB-oauth2server](https://github.com/Erwane/IPB-oauth2server) for some inspiration

## License

GPLv2. See [LICENSE](LICENSE).
