<?php if (!defined('BB2_CORE')) die('Access Denied');

function bb2_admin_pages() {
	if (function_exists('current_user_can')) {
		// The new 2.x way
		if (current_user_can('manage_options')) {
			$bb2_is_admin = true;
		}
	} else {
		// The old 1.x way
		global $user_ID;
		if (user_can_edit_user($user_ID, 0)) {
			$bb2_is_admin = true;
		}
	}

	if ($bb2_is_admin) {
		add_options_page(__(""), __(""), 8, 'bb2_options', 'bb2_options');
	}
}

function bb2_options()
{
	$settings = bb2_read_settings();

	if ($_POST) {
		if ($_POST['display_stats']) {
			$settings['display_stats'] = true;
		} else {
			$settings['display_stats'] = false;
		}
		if ($_POST['strict']) {
			$settings['strict'] = true;
		} else {
			$settings['strict'] = false;
		}
		if ($_POST['verbose']) {
			$settings['verbose'] = true;
		} else {
			$settings['verbose'] = false;
		}
		bb2_write_settings($settings);
?>
	<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php
	}
?>
	<div class="wrap">
	<h2><?php _e("Bad Behavior"); ?></h2>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<fieldset class="options">
	<legend><?php _e('Statistics'); ?></legend>
	<?php bb2_insert_stats(true); ?>
	<p><label><input type="checkbox" name="display_stats" value="true" <?php if ($settings['display_stats']) { ?>checked="checked" <?php } ?>/> <?php _e('Display statistics in blog footer'); ?></label></p>
	</fieldset>

	<fieldset class="options">
	<legend><?php _e('Logging'); ?></legend>
	<p><label><input type="checkbox" name="verbose" value="true" <?php if ($settings['verbose']) { ?>checked="checked" <?php } ?>/> <?php _e('Verbose HTTP request logging'); ?></label></p>
	<legend><?php _e('Strict Mode'); ?></legend>
	<p><label><input type="checkbox" name="strict" value="true" <?php if ($settings['strict']) { ?>checked="checked" <?php } ?>/> <?php _e('Strict checking (blocks more spam but may block some people)'); ?></label></p>
	</fieldset>

	<p class="submit"><input type="submit" name="submit" value="<?php _e('Update &raquo;'); ?>" /></p>
	</form>
	</div>
<?php
}

add_action('admin_menu', 'bb2_admin_pages');

?>
