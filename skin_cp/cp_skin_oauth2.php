<?php

class cp_skin_oauth2 extends output {

//===========================================================================
// <ips:template:desc::trigger:>
//===========================================================================
function listClients($oauthClients) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>{$this->lang->words['o_clients']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'>
				<a href='{$this->settings['base_url']}&amp;{$this->form_code}&amp;do=create'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> Add Client</a>
			</li>
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['o_clients']}</h3>

	<table class='ipsTable'>
		<tr>
			<th width='40%'>{$this->lang->words['o_client_name']}</th>
			<th width='30%'>{$this->lang->words['o_client_url']}</th>
			<th width='20%'>{$this->lang->words['o_client_numusers']}</th>
		</tr>
EOF;

		if (count($oauthClients)) {
			foreach ($oauthClients as $user) {
$IPBHTML .= <<<EOF
		<tr class='ipsControlRow'>
			<td><strong>{$user['api_user_name']}</strong>
			<td><strong style='font-size:14px'>{$user['api_user_key']}</strong>
			<td><strong>{$user['api_user_ip']}</strong>
			<td class='col_buttons'>
				<ul class='ipsControlStrip'>
					<li class='i_edit'><a href='{$this->settings['base_url']}&amp;{$this->form_code}&amp;do=api_edit&amp;api_user_id={$user['api_user_id']}'>{$this->lang->words['a_edit']}</a></li>
EOF;

				if ($this->registry->class_permissions->checkPermission( 'api_remove' )) {
$IPBHTML .= <<<EOF
					<li class='i_delete'><a href='#' onclick='return acp.confirmDelete("{$this->settings['base_url']}&amp;{$this->form_code}&amp;do=api_remove&amp;api_user_id={$user['api_user_id']}");'>{$this->lang->words['a_remove']}</a></li>
EOF;
				}

$IPBHTML .= <<<EOF
				</ul>
			</td>
		</tr>
EOF;
			}
		} else {
$IPBHTML .= <<<EOF
		<tr>
			<td colspan='5' class='no_messages'>
				{$this->lang->words['o_no_clients']} <a href='{$this->settings['base_url']}&amp;{$this->form_code}&amp;do=create' class='mini_button'>{$this->lang->words['o_new_client']}</a>
			</td>
		 </tr>
EOF;
		}

$IPBHTML .= <<<EOF
 	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

function form( $form, $lang, $formcode, $client, $type) {

$IPBHTML = "";
//--starthtml--//

// header
$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>{$lang['title']}</h2>
	<div class='clearfix'></div>
</div>
<div class="section_info">{$this->lang->words['o_create_info']}</div>

<form id='mainform' action='{$this->settings['base_url']}{$this->form_code}&amp;do={$formcode}&amp;client={$api_user['client_id']}' method='post'>
	<div class='acp-box'>
 		<h3>{$lang['subtitle']}</h3>

 		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['a_userbasics']}</th>
			</tr>
EOF;

		if ($type == 'add') {
$IPBHTML .= <<<EOF
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['o_client_id']}</strong>
				</td>
				<td class='field_field'>
					<input type='hidden' name='_client_id' value='{$form['_client_id']}' />
					<strong>{$form['_client_id']}</strong><br />
				</td>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['o_client_secret']}</strong>
				</td>
				<td class='field_field'>
					<input type='hidden' name='_client_secret' value='{$form['_client_secret']}' />
					<strong>{$form['_client_secret']}</strong><br />
				</td>
			</tr>
EOF;
		}

$IPBHTML .= <<<EOF
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['o_client_name']}</strong>
				</td>
				<td class='field_field'>
					{$form['client_name']}<br />
					<span class='desctext'>{$this->lang->words['o_client_name_info']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['o_homepage_uri']}</strong>
				</td>
				<td class='field_field'>
					{$form['homepage_uri']}<br />
					<span class='desctext'>{$this->lang->words['o_homepage_uri_info']}</span>
				</td>
			</tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['o_homepage_description']}</strong>
				</td>
				<td class='field_field'>
					{$form['homepage_description']}<br />
					<span class='desctext'>{$this->lang->words['o_homepage_description_info']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['o_redirect_uri']}</strong>
				</td>
				<td class='field_field'>
					{$form['redirect_uri']}<br />
					<span class='desctext'>{$this->lang->words['o_redirect_uri_info']}</span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['a_grant_functions']}</th>
			</tr>
		</table>
EOF;


$IPBHTML .= <<<EOF
		</ul>

		<div class='acp-actionbar'>
			<input type='submit' value='{$lang['button']}' class='button primary' />
		</div>
	</div>
EOF;


//--endhtml--//
return $IPBHTML;
}

}