<?php
/**
* Settings
*
* Displays a page for modifying the server settings
*
* @package Admin
*/

/**
* Include main system config file 
*/
require_once('../local/config.inc.php');
require_once('setup/testshortURLs.php');

require_once($CONFIG['LANGUAGE_CPATH'].'/admin_strings.inc.php');


//check to see if user is logged in. If not refer to Login page.
authenticate_admins();

//set the required variables
if ($_SERVER['REQUEST_METHOD']=='GET') {
	$message = isset($_GET['message'])? $_GET['message'] : '';
} 
$userlevel_key = $_SESSION['userlevel_key'];
$current_user_key = $_SESSION['current_user_key'];

if (isset($_POST['submit'])) {

	if (!isset($objDb)) {
		if (!class_exists('InteractDb')) {
			require_once('../includes/lib/db.inc.php');
		} 
		$objDb = new InteractDb();
	}
	
	$field_array = array('server_name','default_skin_key', 'default_language', 'error_email', 'no_reply_email', 'email_type', 'sendmail_path', 'sendmail_args', 'email_host', 'email_port', 'email_auth', 'email_username', 'email_password', 'max_file_upload_size', 'keep_trash', 'keep_stale_accounts', 'show_emails', 'secure_server', 'secure_account_creation', 'account_creation_password', 'secret_hash', 'allow_tags', 'default_space_key', 'global_gradebook', 'devolve_account_creation', 'self_delete', 'display_latest', 'usergroup_self_select', 'single_accounts', 'admin_set_skin', 'user_set_skin','admins_add_spaces', 'user_spaces', 'enable_portfolios', 'auth_type', 'proxy_server','proxy_port','proxy_username','proxy_password');
	
	$_POST['max_file_upload_size'] = isset($_POST['max_file_upload_size'])?$_POST['max_file_upload_size']*1048576:'2097152';
	
	$update_sql = $objDb->getUpdateSql('server_settings',$field_array, $_POST);
	$update_sql .= ",short_date_format=".$_POST['short_date_format'].",long_date_format='".$_POST['short_date_format']."'";

	$new_options=0;
	foreach ($_POST['options'] as $val) {$new_options|=$val;}
	$update_sql .= ",options='".$new_options."'";

	$new_short_urls=0;
	foreach ($_POST['short_urls'] as $val) {$new_short_urls|=$val;}
	$update_sql .= ",short_urls='".$new_short_urls."'";
		
	$CONN->Execute($update_sql);
	
	if ($_POST['user_spaces']==1 && isset($_POST['myspace_existing_users']) && $_POST['myspace_existing_users']==1) {
		require_once($CONFIG['BASE_PATH'].'/includes/lib/user.inc.php');
		$objUser = new InteractUser();
		$rs = $CONN->Execute("SELECT {$CONFIG['DB_PREFIX']}users.user_key, first_name, username, owned_by_key FROM {$CONFIG['DB_PREFIX']}users LEFT JOIN {$CONFIG['DB_PREFIX']}spaces ON {$CONFIG['DB_PREFIX']}users.user_key={$CONFIG['DB_PREFIX']}spaces.owned_by_key WHERE user_key!='1' AND username NOT LIKE '_interact_%' AND owned_by_key is null");
		while(!$rs->EOF){
			
			$user_data['first_name'] = $rs->fields[1];
			$user_data['last_name'] = $rs->fields[2];
			$objUser->addMySpace($rs->fields[0], $user_data);
			$rs->MoveNext();
		}
		
	}
	$message = urlencode('Your server settings have been updated');
	header("Location: {$CONFIG['FULL_URL']}/admin/index.php?message=$message");
	exit;
}
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$server_data = $CONN->GetRow("SELECT * FROM {$CONFIG['DB_PREFIX']}server_settings");
	
$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
$t = new Template($CONFIG['TEMPLATES_PATH']);  
$t->set_file(array(

	'header'	=> 'header.ihtml',
	'navigation'=> 'admin/adminnavigation.ihtml',
	'body'	   	=> 'admin/settings.ihtml',
	'footer'	=> 'footer.ihtml'

));

// get page details for titles and breadcrumb navigation
set_common_admin_vars('Update Server Settings', $message);
if (isset($server_data['skin_key'])){
$t->set_var('SKIN_KEY',$server_data['skin_key']);
}
if (!isset($objSkins)) {
	if (!class_exists('InteractSkins')) {
		require_once('../skins/lib.inc.php');
	}
	$objSkins = new InteractSkins();
}
if (!isset($objHtml)) {
	if (!class_exists('InteractHtml')) {
		require_once('../includes/lib/html.inc.php');
	}
	$objHtml = new InteractHtml();
}
$d = dir($CONFIG['BASE_PATH'].'/includes/lib/auth');
$auth_array = array();
while (false !== ($entry = $d->read())) {
	if($entry == "." || $entry == "..") { 
		continue; 
	} 
	if (is_file($CONFIG['BASE_PATH'].'/includes/lib/auth/'.$entry)) {
		$auth_array[str_replace('.inc.php','',$entry)] = str_replace('.inc.php','',$entry);
	}
}
$d->close();
if (empty($server_data['auth_type'])){
	$server_data['auth_type'] = 'dbencrypt';
}
$t->set_var('AUTH_MENU',$objHtml->arrayToMenu($auth_array,'auth_type',$server_data['auth_type'],false,'',false,''));

//create list of alt stylesheets
$skins_array = $objSkins->getSkinArray('view');
// $alt_style_sheets='';
// foreach($skins_array as $key => $value) {
// 	$skin_data = $objSkins->getSkinData($key);
// 	$alt_style_sheets.='<link rel="alternate stylesheet" type="text/css" href="'.$CONFIG['PATH'].'/skins/skin.php?skin_key='.$key.'" title="'.$key.'" />';
// }
// $t->set_var('META_TAGS',$alt_style_sheets);
$t->set_var('SKINS_MENU',$objHtml->arrayToMenu($skins_array,'default_skin_key',$server_data['default_skin_key'],false,'',false,'onChange="changeStyleSheet(this.value)"'));
$t->set_var('EDIT_LANG','<a href="language/index.php">'.$general_strings['add'].'/'.$general_strings['modify'].'</a>');
$t->set_var('ADD_LINK','<a href="../skins/skin_select.php?space_key=1&referer='.$CONFIG['PATH'].'/admin/settings.php?&">'.$general_strings['add'].'/'.$general_strings['modify'].'</a>');
//create any input menus

require_once($CONFIG['INCLUDES_PATH']."/lib/languages.inc.php");
$t->set_var('LANGUAGE_MENU',lang_menu('default_language'));

$spaces_sql = "SELECT name, {$CONFIG['DB_PREFIX']}spaces.space_key FROM {$CONFIG['DB_PREFIX']}spaces, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}spaces.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND {$CONFIG['DB_PREFIX']}module_space_links.status_key='1' ORDER BY name";

$t->set_var('DEFAULT_SPACE_MENU',make_menu($spaces_sql,'default_space_key',$server_data['default_space_key'],'5',false));

$t->set_var('EMAIL_TYPE_MENU',$objHtml->arrayToMenu(array('sendmail' => 'sendmail','smtp' => 'smtp'),'email_type',$server_data['email_type'],false,'',false));

$t->set_var('SMTP_AUTH_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'email_auth',$server_data['email_auth'],false,'',false));

$t->set_var('SECURE_SERVER_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'secure_server',$server_data['secure_server'],false,'',false));

$t->set_var('SECURE_ACCOUNTS_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'secure_account_creation',$server_data['secure_account_creation'],false,'',false));

$t->set_var('LONG_DATE_MENU',$objHtml->arrayToMenu(array(0 => 'd Jan 2003', 1 => 'd January 2003', 2 => 'January d 2003', 3 => 'Jan d 2003'),'long_date_format',$server_data['long_date_format'],false,'',false));

$t->set_var('SHORT_DATE_MENU',$objHtml->arrayToMenu(array(0 => 'dd-mm-yy',1 =>'mm-dd-yy',2 =>'d-m-yy', 3 =>'m-d-yy',4 =>'dd-mm-yyyy', 5 =>'mm-dd-yyyy',6 =>'d-m-yyyy',7 =>'m-d-yyyy'),'short_date_format',$server_data['short_date_format'],false,'',false));

$t->set_var('ADMIN_SET_SKIN_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'admin_set_skin',$server_data['admin_set_skin'],false,'',false));

$t->set_var('USER_SET_SKIN_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'user_set_skin',$server_data['user_set_skin'],false,'',false));

$t->set_var('SHOW_EMAILS_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'show_emails',$server_data['show_emails'],false,'',false));

$t->set_var('DEVOLVE_ACCOUNTS_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'devolve_account_creation',$server_data['devolve_account_creation'],false,'',false));

$t->set_var('SELF_DELETE_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'self_delete',$server_data['self_delete'],false,'',false));

$t->set_var('SINGLE_ACCOUNT_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',2 => 'No'),'single_accounts',$server_data['single_accounts'],false,'',false));

$t->set_var('USERGROUP_SELECT_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'usergroup_self_select',$server_data['usergroup_self_select'],false,'',false));

$t->set_var('ALLOW_TAGS_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'allow_tags',$server_data['allow_tags'],false,'',false));

$t->set_var('GLOBAL_GRADEBOOK_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'global_gradebook',$server_data['global_gradebook'],false,'',false));

$t->set_var('DISPLAY_LATEST_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'display_latest',$server_data['display_latest'],false,'',false));

$t->set_var('ADMINS_ADD_SPACES_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'admins_add_spaces',$server_data['admins_add_spaces'],false,'',false));

$t->set_var('USER_SPACES_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'user_spaces',$server_data['user_spaces'],false,'',false));
$t->set_var('ENABLE_PORTFOLIOS_MENU',$objHtml->arrayToMenu(array(1 => 'Yes',0 => 'No'),'enable_portfolios',$server_data['enable_portfolios'],false,'',false));
$t->set_var('EDIT_NAME_MENU',$objHtml->arrayToMenu(array(2 => 'Yes',0 => 'No'),'options[]', $server_data['options']&2,false,'',false));

for($i=1;$i;$i>>=1) {
	if($server_data['options']&$i) {
		$t->set_var('OPTIONS_'.$i,'checked');
	}
}

$short_url_auto='';
for($i=4;$i;$i>>=1) {
	if($server_data['short_urls']&$i) {$t->set_var('SHORT_URL_'.$i,'checked');}
	$short_url_auto.=($short_urls&$i)?'ON ':'OFF ';
}
$t->set_var('SHORT_URL_AUTO_DETECT',
	($server_data['short_urls']==$short_urls?
		'<label for="short_urls_autod"> </label><span class="formInput" name="short_url_autod">(Matches auto-detected setting)</span>':
		'<label for="short_urls_autod"><span class="message">Warning</span> &mdash; auto-detected setting is</label><span class="formInput" name="short_url_autod">\'<strong>'.substr($short_url_auto,0,-1).'</strong>\'</span>'));

if(!($short_urls & 4)) {
	$t->set_var('SHORT_URL_AUTO_DETECT','<div class="small" align="center" style="clear:both"><b>Note:</b> 404 autodetect does not work when using PHP as a CGI.</div>',true);
}

$server_data['max_file_upload_size'] = $server_data['max_file_upload_size']/1048576;

foreach($server_data as $key => $value) {
	$t->set_var(strtoupper($key),$value);
}

$t->set_var('BUTTON','Modify');

$t->parse('CONTENTS', 'header', true); 
admin_navigation();

$t->parse('CONTENTS', 'body', true);
$t->parse('CONTENTS', 'footer', true);
print_headers();
$t->p('CONTENTS');
$CONN->Close();	   
exit;

?>
