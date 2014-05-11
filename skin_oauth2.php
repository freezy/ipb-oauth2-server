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
	<h1 class='ipsType_pagetitle'>Authorize Application</h1>
	<h2 class='ipsType_subtitle'><a href="{$client['homepage_uri']}" target="_blank">{$client['client_name']}</a> would like your permission to access your account</h2>
	<p>&nbsp;</p>
	<form method="post">
		<input type="submit" class="input_submit" name="authorized" value="Authorize Application"/>
		<input type="submit" class="input_submit ipsButton_secondary" name="authorized" value="Deny"/>
	</form>
	<p>&nbsp;</p>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

}