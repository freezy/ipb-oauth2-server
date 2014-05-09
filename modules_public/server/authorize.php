<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';

class public_oauth2_server_authorize extends ipsCommand {
    public function doExecute(ipsRegistry $registry) {

		$storage = new \IPB\Storage($this->DB);
        print "This would be the authorization page.";
		$storage->getAccessToken('asdf');
    }
}