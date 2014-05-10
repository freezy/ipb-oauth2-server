<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR . 'IPB' . DIRECTORY_SEPARATOR . 'Storage.php';

if (!defined('IN_ACP')) {
	die("<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.");
}

class admin_oauth2_manager_clients extends ipsCommand {

	/** @var cp_skin_oauth2 */
	protected $html;
	protected $form_code;
	protected $form_code_js;

	public function doExecute(ipsRegistry $registry) {

		$this->lang->loadLanguageFile(array('admin_lang'), 'oauth2');
		$this->html = $this->registry->output->loadTemplate('cp_skin_oauth2');

		$this->form_code = $this->html->form_code = 'module=manager&amp;section=clients';
		$this->form_code_js = $this->html->form_code_js = 'module=manager&section=clients';

		switch ($this->request['do']) {
			case 'create':
				$html = $this->createClient();
				break;
			case 'list':
			default:
				$html = $this->html->listClients(array());
				break;
		}

		/*
		 * seriously, this is pathetic. doc[1][2] clearly says to use output::addContent(),
		 * but that only works with non-admin modules. wtf.
		 *
		 * [1] http://www.invisionpower.com/support/guides/_/advanced-and-developers/application/using-skin-templates-r154
		 * [2] http://www.invisionpower.com/support/guides/_/advanced-and-developers/api-methods/outputting-html-r194
		 */
		$this->registry->output->html .= $html;
		$this->registry->output->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
	}

	private function createClient() {
		return $this->form('add');
	}

	private function form($type = 'add') {

		$client_id = $this->request['client_id'];
		$form = array();
		$permissions = array();

		if ($type == 'add') {
			$lang = array(
				'title' => $this->lang->words['o_new_client'],
				'subtitle' => $this->lang->words['o_new_client_short'],
				'button' => $this->lang->words['o_create'],
				'info' => $this->lang->words['o_create_info'],
			);
			$formcode = 'add_save';
			$client = array();
		}

		$form['client_name'] = $this->registry->output->formInput('client_name', !empty($_POST['client_name']) ? stripslashes($_POST['client_name']) : $client['client_name']);
		$form['client_url'] = $this->registry->output->formInput('client_url', !empty($_POST['client_url']) ? stripslashes($_POST['client_url']) : $client['client_url']);

		if ($type == 'add') {
			$form['_client_id'] = substr(md5(mt_rand() . $this->memberData['member_login_key'] . uniqid(mt_rand(), true)), 0, 20);
			$form['_client_secret'] = substr(md5(mt_rand() . $this->memberData['member_login_key'] . uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true)), 0, 40);
		}

		$html = $this->registry->output->global_template->information_box($this->lang->words['a_title'], $this->lang->words['a_msg2']) . "<br />";
		$html .= $this->html->form($form, $lang, $formcode, $client, $type);

		return $html;
	}
}