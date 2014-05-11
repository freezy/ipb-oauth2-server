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
				$html = $this->form('add');
				break;
			case 'save_create':
				$html = $this->save('add');
				break;
			case 'list':
			default:
				$html = $this->ls();
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

	private function ls() {
		return $this->html->listClients(array());
	}

	/**
	 * Displays the edit and creation form for an OAuth2 client.
	 * @param string $type "add" or "update", depending on action.
	 * @return string Form markup
	 */
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
			$formcode = 'save_create';
			$client = array();

			// edit
		} else {

		}

		$form['client_name'] = $this->registry->output->formInput('client_name', !empty($_POST['client_name']) ? stripslashes($_POST['client_name']) : $client['client_name']);
		$form['client_url'] = $this->registry->output->formInput('client_url', !empty($_POST['client_url']) ? stripslashes($_POST['client_url']) : $client['client_url']);
		$form['homepage_uri'] = $this->registry->output->formInput('homepage_uri', !empty($_POST['homepage_uri']) ? stripslashes($_POST['homepage_uri']) : $client['homepage_uri']);
		$form['redirect_uri'] = $this->registry->output->formInput('redirect_uri', !empty($_POST['redirect_uri']) ? stripslashes($_POST['redirect_uri']) : $client['redirect_uri']);
		$form['homepage_description'] = $this->registry->output->formTextarea('homepage_description', !empty($_POST['homepage_description']) ? stripslashes($_POST['homepage_description']) : $client['homepage_description']);

		if ($type == 'add') {
			$form['_client_id'] = substr(md5(mt_rand() . $this->memberData['member_login_key'] . uniqid(mt_rand(), true)), 0, 20);
			$form['_client_secret'] = substr(md5(mt_rand() . $this->memberData['member_login_key'] . uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true)), 0, 40);
		}

		$html = $this->registry->output->global_template->information_box($this->lang->words['a_title'], $this->lang->words['a_msg2']) . "<br />";
		$html .= $this->html->form($form, $lang, $formcode, $client, $type);

		return $html;
	}

	/**
	 * Saves posted data for either adding or creating a new OAuth2 client.
	 *
	 * @param string $type Either "add" or "update", depending on action
	 * @return string Markup of next page.
	 */
	private function save($type = 'add') {

		// form data keys: _client_id, _client_secret, client_name, homepage_uri,
		//                 homepage_description, redirect_uri

		$client_id = $this->request['_client_id'];
		$client_secret = $this->request['_client_secret'];
		$client_name = $this->request['client_name'];
		$homepage_uri = $this->request['homepage_uri'];
		$homepage_description = $this->request['homepage_description'];
		$redirect_uri = $this->request['redirect_uri'];

		// validatons
		if (!$client_name) {
			$this->registry->output->global_message = $this->lang->words['o_please_enter_name'];
			return $this->form($type);
		}

		if ($type == 'add') {
			if (!$client_id) {
				$this->registry->output->global_message = $this->lang->words['o_no_id'];
				return $this->form($type);
			}
			if (!$client_name) {
				$this->registry->output->global_message = $this->lang->words['o_no_secret'];
				return $this->form($type);
			}
			if (!$redirect_uri) {
				$this->registry->output->global_message = $this->lang->words['o_no_redirect'];
				return $this->form($type);
			}
		} else {
			$api_user = $this->DB->buildAndFetch(array('select' => '*', 'from' => 'api_users', 'where' => 'api_user_id=' . $api_user_id));

			if (!$api_user['api_user_id']) {
				$this->registry->output->global_message = $this->lang->words['a_user404'];
				return $this->ls();
			}
		}

		$save = array(
			'redirect_uri' => $redirect_uri,
			'grant_types' => '',
			'scope' => '',
			'user_id' => $this->memberData['member_id'],
		);

		if ($type == 'add') {

			$save['client_id'] = $client_id;
			$save['client_secret'] = $client_secret;

			$this->registry->output->global_message = $this->lang->words['o_created'];
			$this->DB->insert('oauth_clients', $save);
		} else {
			$this->registry->output->global_message = $this->lang->words['o_updated'];
			$this->DB->update('oauth_clients', $save, 'client_id="' . $client_id . '"');
		}
		$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . $this->form_code);
		return $this->ls();
	}
}