<?php

class cp_skin_oauth2 extends output {

//===========================================================================
// <ips:template:desc::trigger:>
//===========================================================================
function listClients($oauthClients) {

$IPBHTML = "";
//--starthtml--//

$status = $this->settings['xmlrpc_enable'] ? "<span class='ipsBadge badge_green'>{$this->lang->words['perf_on']}</span>" : "<a href='{$this->settings['base_url']}&amp;module=settings&amp;section=settings&amp;do=setting_view&amp;conf_title_keyword=xmlrpcapi'><span class='ipsBadge badge_red'>{$this->lang->words['perf_off']}</span></a>";

$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>OAuth2 Clients</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'>
				<a href='{$this->settings['base_url']}&amp;{$this->form_code}&amp;do=api_add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> Add Client</a>
			</li>
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>OAuth2 Clients</h3>

	<table class='ipsTable'>
		<tr>
			<th width='40%'>Application Name</th>
			<th width='30%'>URL</th>
			<th width='20%'>Users</th>
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
				{$this->lang->words['a_nousers']} <a href='{$this->settings['base_url']}&amp;{$this->form_code}&amp;do=api_add' class='mini_button'>{$this->lang->words['a_createone']}</a>
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
}