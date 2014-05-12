<?php

class skin_helloworld extends output {

//===========================================================================
// <ips:template:desc::trigger:>
//===========================================================================
function authorize($client) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<img src='{$client['homepage_logo']}' class='ipsUserPhoto ipsUserPhoto_medium left' /></a>
<div class='ipsBox_withphoto'>
	<h1 class='ipsType_pagetitle'>{$this->lang->words['o_authorize_application']}</h1>
	<h2 class='ipsType_subtitle'><a href="{$client['homepage_uri']}" target="_blank">{$client['client_name']}</a> {$this->lang->words['o_would_like_to_use']}</h2>
	<p>&nbsp;</p>
	<form method="post">
		<input type="submit" class="input_submit" name="authorized" value="{$this->lang->words['o_authorize_application']}"/>
		<input type="submit" class="input_submit ipsButton_secondary" name="authorized" value="{$this->lang->words['o_deny']}"/>
	</form>
	<p>&nbsp;</p>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

}