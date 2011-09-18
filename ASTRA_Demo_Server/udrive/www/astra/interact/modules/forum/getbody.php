<?php
require_once('../../local/config.inc.php');
require_once('../../includes/lib/user.inc.php');
require_once('../../includes/pear/JSON.php');
$objUser = new InteractUser();
$post_key = isset($_GET['post_key']) ? $_GET['post_key'] : '';
if (empty($post_key)) {
	header("Status: 404 Not Found");	
	exit;
}
if (empty($_SESSION['open_posts']) || !is_array($_SESSION['open_posts'])) {
	$_SESSION['open_posts'] = array($post_key);
} else {
	array_push($_SESSION['open_posts'],$post_key);
}
$CONN->SetFetchMode('ADODB_FETCH_ASSOC');
$post_data = $CONN->GetRow("SELECT {$CONFIG['DB_PREFIX']}posts.post_key, {$CONFIG['DB_PREFIX']}posts.module_key, {$CONFIG['DB_PREFIX']}module_space_links.space_key as space_key, thread_key, body, added_by_key FROM {$CONFIG['DB_PREFIX']}posts, {$CONFIG['DB_PREFIX']}module_space_links WHERE {$CONFIG['DB_PREFIX']}posts.module_key={$CONFIG['DB_PREFIX']}module_space_links.module_key AND post_key='$post_key'");

if (!$post_data) {
	header("Status: 404 Not Found");	
	exit;
} else {
	//$CONN->Execute("INSERT INTO debug(messages) values ('{$post_data['body']}')");
	/*
	require_once($CONFIG['LANGUAGE_CPATH'].'/forum_strings.inc.php');
	require_once($CONFIG['TEMPLATE_CLASS_PATH'].'/template.inc');
	$t = new Template($CONFIG['TEMPLATES_PATH']);  
	$t->set_file(array('postbody' => 'forums/postbody.ihtml',));
	$t->set_var('FULL_URL',  $CONFIG['FULL_URL']);
	$t->set_var('PHOTO_TAG',  $objUser->getUserPhotoTag($post_data['added_by_key'], $width='40', '', $align='middle'));	
	$t->set_strings('postbody',  $forum_strings, $post_data, '');
	$t->parse('CONTENTS', 'postbody', true);
	$t->p('CONTENTS');
	*/
	
	// create a new instance of Services_JSON
	$post_data = array('post_key'=>$post_key, 'body' => $post_data['body'], 'photo_tag' => $objUser->getUserPhotoTag($post_data['added_by_key'], $width='40', '', $align='middle'));
	$json = new Services_JSON();
	$output = $json->encode($post_data);
	print($output);
	exit;
}
?>
