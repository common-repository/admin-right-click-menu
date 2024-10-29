<?php
/*
Plugin Name: Admin Right Click Menu
Plugin URI: http://knalle.dk
Description: The default WP Admin Toolbar (frontend) is moved to a right click menu (context menu)
Author: Thor Sarup
License: GPL3
Version: 1.3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License 
along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
*/

// remove deafult css body modifications
add_action('get_header', 'arcm_remove_admin_login_header');
function arcm_remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}
// CSS and JS
add_action('wp_footer', 'arcm_enqueue_css_js');
function arcm_enqueue_css_js() {	
	if ( is_admin_bar_showing() ) {
		wp_enqueue_style( 'arcm_style', plugins_url( 'css/arcm.css', __FILE__ ));
		$arcm_options = get_option( 'arcm_options', false );
		if ($arcm_options) { 
			if (isset($arcm_options['selectors'])) { 
				?><script>
					var arcm_selector = "<?php echo $arcm_options['selectors']?>";
				</script><?php
			}
		}
		wp_enqueue_script( 'arcm_js', plugins_url( 'js/arcm.js', __FILE__ ),array('jquery'));
	}
}
// 'Settings' link from plugin page
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'arcm_plugin_action_links' );
function arcm_plugin_action_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/options-general.php?page=arcm_options' ) ) . '">' . __( 'Settings', 'arcm' ) . '</a>'
	), $links );
	return $links;
}

// Do it!
add_action( 'wp_before_admin_bar_render', 'arcm_remove_wp_toolbar_links' );
function arcm_remove_wp_toolbar_links() {
	global $wp_admin_bar;
	if ( is_admin_bar_showing() ) {
		$arcm_options = get_option( 'arcm_options', false );
		if ($arcm_options) {
			if (isset($arcm_options['hide_wp_logo'])) {
				if ( ( $arcm_options['hide_wp_logo'] == 1 && !is_admin() ) || $arcm_options['hide_wp_logo'] == 2 )
					$wp_admin_bar->remove_menu('wp-logo');
			}
			if (isset($arcm_options['hide_customize_menu'])) {
				if ( ( $arcm_options['hide_customize_menu'] == 1 && !is_admin() ) )
					$wp_admin_bar->remove_menu('customize');
			}
			if (isset($arcm_options['hide_comments_menu'])) {
				if ( ( $arcm_options['hide_comments_menu'] == 1 && !is_admin() ) || $arcm_options['hide_comments_menu'] == 2 )
					$wp_admin_bar->remove_menu('comments');
			}
			if (isset($arcm_options['hide_new_menu'])) {
				if ( ( $arcm_options['hide_new_menu'] == 1 && !is_admin() ) || $arcm_options['hide_new_menu'] == 2 )
					$wp_admin_bar->remove_menu('new-content');
			}
			if (isset($arcm_options['hide_edit_menu'])) {
				if ( ( $arcm_options['hide_edit_menu'] == 1 && !is_admin() ) )
					$wp_admin_bar->remove_menu('edit');
			}
			if (isset($arcm_options['hide_search_field'])) {
				if ( ( $arcm_options['hide_search_field'] == 1 && !is_admin() ) )
					$wp_admin_bar->remove_menu('search');
			}
			if (isset($arcm_options['hide_updates_menu'])) {
				if ( ( $arcm_options['hide_updates_menu'] == 1 && !is_admin() ) || $arcm_options['hide_updates_menu'] == 2 )
					$wp_admin_bar->remove_menu('updates');
			}
			if (isset($arcm_options['hide_account_menu'])) {
				if ( ( $arcm_options['hide_account_menu'] == 1 && !is_admin() ) || $arcm_options['hide_account_menu'] == 2 )
					$wp_admin_bar->remove_menu('my-account');
			}
		}
	}
}


/////
// https://developer.wordpress.org/plugins/settings/creating-and-using-options/
// Add options submenu link under Settings
/////
function arcm_add_options_submenu_page() {
     add_submenu_page(
		'options-general.php',					// admin page slug
		'Admin Right Click Menu Settings',		// page title
		'Admin Right Click Menu',				// menu title
		'manage_options',						// capability required to see the page
		'arcm_options',							// admin page slug, e.g. options-general.php?page=arcm_options
		'arcm_options_page'						// callback function to display the options page
     );
}
add_action( 'admin_menu', 'arcm_add_options_submenu_page' );
 
/////
//Register the settings
////
function arcm_register_settings() {
     register_setting(
          'arcm_options',		// settings section
          'arcm_options'		// setting name
     );
}
add_action( 'admin_init', 'arcm_register_settings' );
 
////
// Build the options page
////
function arcm_options_page() {
	if ( ! isset( $_REQUEST['settings-updated'] ) )
	$_REQUEST['settings-updated'] = false; ?>
	<style>
		#rightclickmenu .icon {
			position: relative;
			float: left;
			font: 400 20px/1 dashicons;
			speak: none;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			background-image: none!important;
			margin-right: 6px;
		}
		.icon:before { position: relative; top: 1px; }
		.icon.wp-logo:before { 	   content: "\f120"; }
		.icon.customize:before {   content: "\f540"; }
		.icon.new:before {		   content: "\f132"; }
		.icon.edit:before {		   content: "\f464"; }
		.icon.comments:before {	   content: "\f101"; }
		.icon.searchfield:before { content: "\f179"; }
		.icon.profile:before {	   content: "\f110"; }
		.icon.updates:before {	   content: "\f463"; }
	</style>
	<div class="wrap">

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		   <div class="updated fade"><p><strong><?php _e( 'arcm options saved!', 'arcm' ); ?></strong></p></div>
		<?php endif; ?>

		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<div id="rightclickmenu">
			<div id="post-body">
				<div id="post-body-content">
					<form method="post" action="options.php">
						<?php settings_fields( 'arcm_options' ); ?>
						<?php $options = get_option( 'arcm_options' ); ?>
						<table class="form-table">
							<tr valign="top"><th scope="row"><?php _e( 'Right click activates menu on:', 'arcm' ); ?></th>
								<td>
									<?php $input_selector = sanitize_text_field( $options['selectors'] ); ?>
									<input class="regular-text" id="selectors" name="arcm_options[selectors]" value="<?php echo $input_selector ?>" type="text"><br />
									<label class="description" for="arcm_options[selectors]"><?php _e( 'Enter CSS selector (elements, classes, ids)', 'arcm' ); ?><br /><?php _e( 'E.g. .site-title, .logo, header, body'); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon wp-logo"></span><?php _e( 'Hide the WordPress logo menu?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_wp_logo]" id="hide-wp-logo-menu">
										<?php $selected = $options['hide_wp_logo']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
										<option value="2" <?php selected( $selected, 2 ); ?> >Yes, also remove in backend</option>
									</select><br />
									<label class="description" for="arcm_options[hide_wp_logo]"><?php _e( 'Hide the WordPress logo and sub menu.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon customize"></span><?php _e( 'Hide <b>\'Customize\'</b> menu?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_customize_menu]" id="hide-customize-menu">
										<?php $selected = $options['hide_customize_menu']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
									</select><br />
									<label class="description" for="arcm_options[hide_customize_menu]"><?php _e( 'Hide the <b>\'Customize\'</b> and sub menu.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon updates"></span><?php _e( 'Hide updates?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_updates_menu]" id="hide-updates-menu">
										<?php $selected = $options['hide_updates_menu']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
										<option value="2" <?php selected( $selected, 2 ); ?> >Yes, also remove in backend</option>
									</select><br />
									<label class="description" for="arcm_options[hide_updates_menu]"><?php _e( 'Removes update icon.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon comments"></span><?php _e( 'Hide Comments Bubble?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_comments_menu]" id="hide-comments-menu">
										<?php $selected = $options['hide_comments_menu']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
										<option value="2" <?php selected( $selected, 2 ); ?> >Yes, also remove in backend</option>
									</select><br />
									<label class="description" for="arcm_options[hide_comments_menu]"><?php _e( 'Hide comments bubble and comments counter.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon new"></span><?php _e( 'Hide New menu?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_new_menu]" id="hide-new-menu">
										<?php $selected = $options['hide_new_menu']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
										<option value="2" <?php selected( $selected, 2 ); ?> >Yes, also remove in backend</option>
									</select><br />
									<label class="description" for="arcm_options[hide_new_menu]"><?php _e( 'Removes <b>\'+ New\'</b> and sub menu.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon edit"></span><?php _e( 'Hide Edit menu?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_edit_menu]" id="hide-edit-menu">
										<?php $selected = $options['hide_edit_menu']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
									</select><br />
									<label class="description" for="arcm_options[hide_edit_menu]"><?php _e( 'Removes <b>\'Edit\'</b>.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon profile"></span><?php _e( 'Hide my account menu?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_account_menu]" id="hide-account-menu">
										<?php $selected = $options['hide_account_menu']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
										<option value="2" <?php selected( $selected, 2 ); ?> >Yes, also remove in backend</option>
									</select><br />
									<label class="description" for="arcm_options[hide_account_menu]"><?php _e( 'Removes <b>\'Howdy, username\'</b> and sub menu.', 'arcm' ); ?></label>
								</td>
							</tr>
							<tr valign="top"><th scope="row"><span class="icon searchfield"></span><?php _e( 'Hide Search field?', 'arcm' ); ?></th>
								<td>
									<select name="arcm_options[hide_search_field]" id="hide-search-field">
										<?php $selected = $options['hide_search_field']; ?>
										<option value="0" <?php selected( $selected, 0 ); ?> >No</option>
										<option value="1" <?php selected( $selected, 1 ); ?> >Yes</option>
									</select><br />
									<label class="description" for="arcm_options[hide_search_field]"><?php _e( 'Removes the search field.', 'arcm' ); ?></label>
								</td>
							</tr>
						</table>
						<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
					</form>
				</div>
				<p><strong>Note:</strong> Admin Right Click Menu only appears if the <a href="<?php echo esc_url( admin_url( '/profile.php' ) )?>">Show Toolbar when viewing site</a> is checked for the user.
			</div>
		</div>
	</div>
<?php }
