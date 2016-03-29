<?php
/**
 * Plugin Name: Backup/Restore Divi Theme Options
 * Description: Backup & Restore your Divi Theme Options.
 * Theme URI: https://github.com/SiteSpace/backup-restore-divi-theme-options
 * Author: Divi Space
 * Author URI: http://divispace.com
 * Version: 1.0.2
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Tags: divi, theme options, theme settings, divi theme options, divi options, divi theme settings, divi settings
 * Text Domain: backup-restore-divi-theme-options
 */

// Main plugins class
class backup_restore_divi_theme_options {

  // Register a plugin menu in the main admin menu.  
	function backup_restore_divi_theme_options() {
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}

  // Registration activities performed by menu plugin.
	function admin_menu() {

    // Generate submenu page
		$page = add_submenu_page('tools.php', 'Backup/Restore Theme Options', 'Backup/Restore Theme Options', 'manage_options', 'backup-restore-divi-theme-options', array(&$this, 'options_page'));

    // Registration call of action for imports and exports.
		add_action("load-{$page}", array(&$this, 'import_export'));

    // Register a submenu page for plugin main menu. 
		add_submenu_page( 'et_divi_options',__( 'Backup/Restore Theme Options', 'Divi' ), __( 'Backup/Restore Theme Options', 'Divi' ), 'manage_options', 'tools.php?page=backup-restore-divi-theme-options', 'backup-restore-divi-theme-options' );

	}
	function import_export() {

    // Download actions, return backup file to users.  
		if (isset($_GET['action']) && ($_GET['action'] == 'download')) {
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header("Content-Type: text/plain");
			header('Content-Disposition: attachment; filename="divi-theme-options-'.date("dMy").'.dat"');
			echo serialize($this->_get_options());
			die();
		}

    // Restore actions, get file from users after all checks. 
		if (isset($_POST['upload']) && check_admin_referer('shapeSpace_restoreOptions', 'shapeSpace_restoreOptions')) {

      // Check attached files if exists - restore then. 
			if ($_FILES["file"]["error"] > 0) {
				// error
			} else {
        // get files 
				$options = unserialize(file_get_contents($_FILES["file"]["tmp_name"]));
				if ($options) {
          // upload and save all files 
					foreach ($options as $option) {
						update_option($option->option_name, unserialize($option->option_value));
					}
				}
			}
      // After user is upload files, return user to a plugin page. 
			wp_redirect(admin_url('tools.php?page=backup-restore-divi-theme-options'));
			exit;
		}
	}
  
  // Generate html for a plugin action page 
	function options_page() { ?>

		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Backup/Restore Theme Options</h2>
			<form action="" method="POST" enctype="multipart/form-data">
				<style>#backup-restore-divi-theme-options td { display: block; margin-bottom: 20px; }</style>
				<table id="backup-restore-divi-theme-options">
					<tr>
						<td>
							<h3>Backup/Export</h3>
							<p>Here are the stored settings for the current theme:</p>
							<p><textarea disabled class="widefat code" rows="20" cols="100" onclick="this.select()"><?php echo serialize($this->_get_options()); ?></textarea></p>
							<p><a href="?page=backup-restore-divi-theme-options&action=download" class="button-secondary">Download as file</a></p>
						</td>
						<td>
							<h3>Restore/Import</h3>
							<p><label class="description" for="upload">Restore a previous backup</label></p>
							<p><input type="file" name="file" /> <input type="submit" name="upload" id="upload" class="button-primary" value="Upload file" /></p>
							<?php if (function_exists('wp_nonce_field')) wp_nonce_field('shapeSpace_restoreOptions', 'shapeSpace_restoreOptions'); ?>
						</td>
					</tr>
				</table>
			</form>
		</div>

	<?php }

  // Call for options initializations and serializations then. 
	function _display_options() {
		$options = unserialize($this->_get_options());
	}

  // Get plugin options from database. 
	function _get_options() {
		global $wpdb;
		return $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name = 'et_divi'"); // edit 'shapeSpace_options' to match theme options
	}
}

// initialize plugin 
new backup_restore_divi_theme_options();
?>
