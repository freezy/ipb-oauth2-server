<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Server.php';

class public_oauth2_server_profile extends ipsCommand {

	public function doExecute(ipsRegistry $registry) {

		// setup storage and server
		$storage = new IPB\Storage($this->DB, $this->memberData['member_id']);
		$server = IPB\Server::createServer($storage);

		$request = OAuth2\Request::createFromGlobals();

		// validate token
		if (!$server->verifyResourceRequest($request)) {
			$server->getResponse()->send();
			die;
		}

		$token = $server->getAccessTokenData($request);
		header('Content-Type: application/json');

		// create profile object
		if ($member = IPSMember::load($token['member_id'], 'all')) {
			$profile = array(
				'id' => $member['member_id'],
				'username' => $member['name'],
				'displayName' => $member['members_display_name'],
				'email' => $member['email'],
				'profileUrl' => $this->settings['board_url'] . '/index.php?showuser=' . $token['member_id'],
				'avatar' => array(
					'thumb' => array(
						'url' => $this->settings['upload_url'] . '/' . $member['pp_thumb_photo'],
						'width' => $member['pp_thumb_width'],
						'height' => $member['pp_thumb_height']
					),
					'full' => array(
						'url' => $this->settings['upload_url'] . '/' . $member['pp_main_photo'],
						'width' => $member['pp_main_width'],
						'height' => $member['pp_main_height']
					),
				),
			);
			echo json_encode($profile);
		} else {
			http_response_code(404);
			die;
		}
	}
}