<?php
require_once('../../local/config.inc.php');
if ($_SERVER['REQUEST_METHOD']=='POST') {
	
	require_once('../../includes/lib/user.inc.php');
	$objUser = new InteractUser();
	$post_key = $_POST['parent_key'];
	$added_by_key = $_SESSION['current_user_key'];
	$post_data['subject'] = $_POST['qRSubject'.$post_key];
	$post_data['body'] = $_POST['qRBody'.$post_key];
	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array('postbody' => 'forums/postbody.ihtml',));
	$t->set_var('FULL_URL',  $CONFIG['FULL_URL']);
	$t->set_var('POST_KEY_VALUE',  rand(0,3000));
	$t->set_var('PHOTO_TAG',  $objUser->getUserPhotoTag($added_by_keye , $width='40', '', $align='middle'));	
	$t->set_strings('postbody',  $forum_strings, $post_data, '');
	$t->parse('CONTENTS', 'postbody', true);
	$t->p('CONTENTS');
	
	
	exit;
}
$post_key = isset($_GET['post_key']) ? $_GET['post_key'] : '';
if (empty($post_key)) {
	header("Status: 404 Not Found");	
	exit;
}
$CONN->SetFetchMode('ADODB_FETCH_ASSOC');
$post_data = $CONN->GetRow("SELECT {$CONFIG['DB_PREFIX']}posts.post_key, {$CONFIG['DB_PREFIX']}posts.module_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key as space_key, thread_key FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND post_key='$post_key'");

if (!$post_data) {
	header("Status: 404 Not Found");	
	exit;
} else {
	require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_var('REPLY_ID',rand(1,100));
	$t->set_file(array('quickreply' => 'forums/quickreply.ihtml',));
	$t->set_strings('quickreply',  $forum_strings, $post_data, '');
	$t->parse('CONTENTS', 'quickreply', true);
	$t->p('CONTENTS');
	exit;
}
?>