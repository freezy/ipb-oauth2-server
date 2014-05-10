<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';

if (!defined('IN_ACP')) {
	die("<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.");
}

class admin_oauth2_manager_clients extends ipsCommand {

	/** @var skin_oauth2 */
	protected $html;
	protected $form_code;
	protected $form_code_js;


	public function doExecute(ipsRegistry $registry) {

		$this->html = $this->registry->output->loadTemplate('cp_skin_oauth2');

		$this->form_code	= $this->html->form_code	= 'module=manager&amp;section=clients';
		$this->form_code_js	= $this->html->form_code_js	= 'module=manager&section=clients';

		/*
		 * seriously, this is pathetic. doc[1][2] clearly says to use output::addContent(),
		 * but that only works with non-admin modules. wtf.
		 *
		 * [1] http://www.invisionpower.com/support/guides/_/advanced-and-developers/application/using-skin-templates-r154
		 * [2] http://www.invisionpower.com/support/guides/_/advanced-and-developers/api-methods/outputting-html-r194
		 */
//		$this->registry->output->html_main .= $this->registry->output->getTemplate('oauth2')->listClients(array());
		$this->registry->output->html .= $this->html->listClients(array());

		$this->registry->output->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
	}
}