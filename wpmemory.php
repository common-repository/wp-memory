<?php /*
Plugin Name: WP Memory
Plugin URI: http://wpmemory.com
Description: Check for high memory usage, include the results on the Site Health page, and provide suggestions.
Version: 3.51
Author: Bill Minozzi
Domain Path: /language
Author URI: http://billminozzi.com
Text Domain: wp-memory
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if (!defined('ABSPATH')) {
	die('We\'re sorry, but you can not directly access this file.');
}
$wpmemory_php_memory_limit = (int) get_option('wpmemory_php_memory_limit', '0');
if ($wpmemory_php_memory_limit > 0 and $wpmemory_php_memory_limit <= 1024) {
	// @ini_set('memory_limit', $wpmemory_php_memory_limit . 'M');
}
define('WPMEMORYURL', plugin_dir_url(__file__));
$wpmemory_request_url = trim(sanitize_url($_SERVER['REQUEST_URI']));

$plugin = plugin_basename(__FILE__);
define('WPMEMORYPATH', plugin_dir_path(__file__));
define('WPMEMORYDOMAIN', get_site_url());
define('WPMEMORYIMAGES', plugin_dir_url(__file__) . 'images');
define('WPMEMORYPAGE', trim(sanitize_text_field($GLOBALS['pagenow'])));
define('WPMEMORYHOMEURL', admin_url());
define('WPMEMORYADMURL', admin_url());
$wpmemory_request_url = sanitize_url($_SERVER['REQUEST_URI']);

// require_once WPMEMORYPATH . 'functions/bill-catch-errors.php';

if (is_admin())
	add_action('plugins_loaded', 'wpmemory_localization_init');


$wpmemory_plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$wp_memory_plugin_version = $wpmemory_plugin_data['Version'];
define('WPMEMORYVERSION', $wp_memory_plugin_version);

$wpmemory_activated_notice =  trim(sanitize_text_field(get_option('wpmemory_activated_notice', '0')));
$wpmemory_was_activated =  trim(sanitize_text_field(get_option('wpmemory_was_activated', '0')));
$wp_memory_update = trim(sanitize_text_field(get_option('wp_memory_update', '')));

function wpmemory_add_admstylesheet()
{
	global $wpmemory_request_url;

	$pos = strpos($wpmemory_request_url, 'page=wp_memory_admin_page');
	$pos2 = strpos($wpmemory_request_url, 'wp-admin/index.php');
	if ($pos !== false or $pos2 !== false) {
		wp_enqueue_script('jquery');






		//wp_enqueue_style('datatables-style', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
		//wp_enqueue_script('jquery');
		//wp_enqueue_script('datatables-script', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array('jquery'), '1.10.25', true);




		wp_enqueue_script('wpmah-flot', WPMEMORYURL .
			'js/jquery.flot.min.js', array('jquery'));
		wp_enqueue_script('wpmflotpie', WPMEMORYURL .
			'js/jquery.flot.pie.js', array('jquery'));
		wp_enqueue_script('wpmcircle', WPMEMORYURL .
			'js/radialIndicator.js', array('jquery'));

		wp_register_script("wpmemory-cookies", WPMEMORYURL . 'js/c_o_o_k_i_e.js', array('jquery'), WPMEMORYVERSION, true);
		wp_enqueue_script('wpmemory-cookies');

		// wp_register_script("wpmemory-dismiss", WPMEMORYURL . 'js/dismiss.js', array('jquery'), WPMEMORYVERSION, true);
		// wp_enqueue_script('wpmemory-dismiss');



	}

	wp_register_script("wpmemory-dismiss", WPMEMORYURL . 'js/dismiss.js', array('jquery'), WPMEMORYVERSION, true);
	wp_enqueue_script('wpmemory-dismiss');

	wp_register_style('wpmemory ', plugin_dir_url(__FILE__) . '/css/wpmemory.css');
	wp_enqueue_style('wpmemory ');
}
if (is_admin()) {
	add_action('admin_init', 'wpmemory_add_admstylesheet');
	// Activation...
	register_activation_hook(__FILE__, 'wpmemory_activated');
}

add_filter("plugin_action_links_$plugin", 'wp_memory_plugin_settings_link');




/*
$wpmemory_memory['limit'] = (int) ini_get('memory_limit');
$wpmemory_memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 0) : 0;
if (!is_numeric($wpmemory_memory['usage']) or $wpmemory_memory['usage'] < 1) {
    $wpmemory_memory['usage'] = 1;
}
$wpmemory_mb = 'MB';
if (defined("WP_MEMORY_LIMIT")) {
    $wpmemory_memory['wp_limit'] = trim(WP_MEMORY_LIMIT);
    $wpmemory_memory['wp_limit'] = substr($wpmemory_memory['wp_limit'], 0, strlen($wpmemory_memory['wp_limit']) - 1);
} else {
    $wpmemory_memory['wp_limit'] = 40;
}
if (!is_numeric($wpmemory_memory['wp_limit'])) {
    $wpmemory_memory['wp_limit'] = 40;
}
$perc = $wpmemory_memory['usage'] / $wpmemory_memory['wp_limit'];
// $perc = 100;
if ($perc > .7) {
    $wpmemory_memory['color'] = 'red';
} else {
    $wpmemory_memory['color'] = 'green';
}
$wpmemory_usage_content = __('Current memory WordPress Limit', 'wp-memory' ) . ': ' . $wpmemory_memory['wp_limit'] . $wpmemory_mb . '&nbsp;&nbsp;&nbsp;  |&nbsp;&nbsp;&nbsp;';
$wpmemory_usage_content .= '<span style="color:' . $wpmemory_memory['color'] . ';">';
$wpmemory_usage_content .= 'Your usage now: ' . $wpmemory_memory['usage'] .
    'MB &nbsp;&nbsp;&nbsp;';
$wpmemory_usage_content .= '</span>';
$wpmemory_usage_content .= '<br />';
$wpmemory_usage_content .= '</strong>';
if ($perc > .7) {
    $wpmemory_label = 'Critical';
    $wpmemory_status = 'critical';
    $wpmemory_description = $wpmemory_usage_content . sprintf('<p>%s</p>', __('Run your site with High Memory Usage, can result in behaving slowly, or pages fail to load, you get random white screens of death or 500 internal server error. Basically, the more content, features and plugins you add to your site, the bigger your memory limit has to be. Increase the WP Memory Limit is a standard practice in WordPress. You can manually increase memory limit in WordPress by editing the wp-config.php file. You can find instructions in the official WordPress documentation (Increasing memory allocated to PHP). Just click the link below: ', 'wp-memory' ));
    $wpmemory_actions = sprintf('<p><a href="%s">%s</a></p>', 'https://codex.wordpress.org/Editing_wp-config.php', __('WordPress Help Page', 'wp-memory' ));
} else {
    $wpmemory_label = 'Performance';
    $wpmemory_status = 'good';
    $wpmemory_description = __('Pass', 'wp-memory' ) . '.';
    $wpmemory_actions =     '';
}
*/



require_once WPMEMORYPATH . "functions/functions.php";
if (is_admin()) {
	//debug();
	require_once(WPMEMORYPATH . 'includes/help/help.php');
}
// add_filter('site_status_tests', 'wpmemory_add_memory_test');
register_activation_hook(__FILE__, 'wpmemory_activation');
add_filter('debug_information', 'wpmemory_add_debug_info');
register_activation_hook(__FILE__, 'wpmemory_admin_notice_activation_hook');
add_action('admin_notices', 'wp_memory_activ_message');
add_action('admin_menu', 'wp_memory_init');


if (!function_exists('wp_get_current_user')) {
	require_once(ABSPATH . "wp-includes/pluggable.php");
}


function wpmemory_install_required_extensions()
{
	global $plugin_required;
	if (empty($plugin_required))
		return;

	echo '<div class="notice notice-warning is-dismissible">';
	echo '<br /><b>';
	echo esc_attr__('Message from WP Memory', 'wp-memory');
	echo ':</b><br />';
	echo esc_attr__('To Install the extension:', 'wp-memory');
	echo ' ' . esc_attr($plugin_required);
	echo '<br />';
	echo ' <a class="button button-primary" href="plugins.php?page=tgmpa-install-plugins">';
	echo esc_attr__('click here', "wp-memory");
	echo '</a>';
	echo '<br /><br /></div>';
}

/* =============================== */



if (!function_exists('wp_get_current_user')) {
	require_once(ABSPATH . "wp-includes/pluggable.php");
}

/* =============================== */
function wpmemory_new_more_plugins()
{
	$plugin = new wpmemory_Bill_show_more_plugins();
	$plugin->bill_show_plugins();
}
function wpmemory_plugin_installed($slug)
{
	$all_plugins = get_plugins();
	foreach ($all_plugins as $key => $value) {
		$plugin_file = $key;
		$slash_position = strpos($plugin_file, '/');
		$folder = substr($plugin_file, 0, $slash_position);
		// match FOLDER against SLUG
		if ($slug == $folder) {
			return true;
		}
	}
	return false;
}


function wpmemory_load_upsell()
{

	//wp_enqueue_style('wpmemory-more', WPMEMORYURL . 'includes/more/more.css');
	//wp_register_script('wpmemory-more-js', WPMEMORYURL . 'includes/more/more.js', array('jquery'));
	//wp_enqueue_script('wpmemory-more-js');
	$wpmemory_bill_go_pro_hide = trim(get_option('bill_go_pro_hide'));





	// $wpmemory_bill_go_pro_hide = '';
	// Debug ...
	$wtime = strtotime('-08 days');
	// update_option('wpmemory_bill_go_pro_hide', $wtime);
	if (empty($wpmemory_bill_go_pro_hide)) {
		$wtime = strtotime('-05 days');
		update_option('bill_go_pro_hide', $wtime);
		$wpmemory_bill_go_pro_hide =  $wtime;
	}

	if (strlen($wpmemory_bill_go_pro_hide) < 10)
		$wpmemory_bill_go_pro_hide = strtotime($wpmemory_bill_go_pro_hide);



	$now = time();
	$delta = $now - $wpmemory_bill_go_pro_hide;

	// debug
	// $delta = time();
	/*
    if ($delta > (3600 * 24 * 6)) {
	   $list = 'enqueued';
	   if( !wp_script_is( 'bill-css-vendor-fix', $list ) ) {
		require_once(WPMEMORYPATH . 'includes/vendor/vendor.php');
		wp_enqueue_style('bill-css-vendor-fix', WPMEMORYURL . 'includes/vendor/vendor_fix.css');

		wp_register_script("bill-js-vendor", WPMEMORYURL . 'includes/vendor/vendor.js', array('jquery'), WPMEMORYVERSION, true);
		wp_enqueue_script('bill-js-vendor');

	   }
    }
	*/

	wp_register_script("bill-js-vendor-sidebar", WPMEMORYURL . 'includes/vendor/vendor-sidebar.js', array('jquery'), WPMEMORYVERSION, true);
	wp_enqueue_script('bill-js-vendor-sidebar');

	wp_enqueue_style('bill-css-vendor-wpm', WPMEMORYURL . 'includes/vendor/vendor.css');
	// var_dump(__LINE__);
}

if (!function_exists('wp_get_current_user')) {
	require_once(ABSPATH . "wp-includes/pluggable.php");
}
if (is_admin() or is_super_admin()) {
	add_action('admin_enqueue_scripts', 'wpmemory_load_upsell');
	add_action('wp_ajax_wpmemory_install_plugin', 'wpmemory_install_plugin');
}

function wpmemory_install_plugin()
{


	if (!current_user_can('manage_options')) {
		// O usuário é administrador, execute o código aqui
		die("User not admin!");
	}





	if (isset($_POST['nonce'])) {
		$nonce = sanitize_text_field($_POST['nonce']);
		//if ( ! wp_verify_nonce( $nonce, 'wpmemory_install_plugin' ) ) 
		// die('Bad Nonce');
	} else
		wp_die('nonce not set');


	if (isset($_POST['slug'])) {
		$slug = sanitize_text_field($_POST['slug']);
	} else {
		echo 'Fail error (-5)';
		wp_die();
	}

	if ($slug != "antibots" && $slug != "site-checkup" && $slug != "database-backup" &&  $slug != "bigdump-restore" &&  $slug != "easy-update-urls" &&  $slug != "s3cloud" &&  $slug != "toolsfors3" && $slug != "antihacker" && $slug != "toolstruthsocial" && $slug != "stopbadbots" && $slug != "wptools" && $slug != "recaptcha-for-all" && $slug != "wp-memory") {
		wp_die('wrong slug');
	}

	$plugin['source'] = 'repo'; // $_GET['plugin_source']; // Plugin source.
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes.
	// get plugin information
	$api = plugins_api('plugin_information', array('slug' => $slug, 'fields' => array('sections' => false)));
	if (is_wp_error($api)) {
		echo 'Fail error (-1)';
		wp_die();
		// proceed
	} else {
		// Set plugin source to WordPress API link if available.
		if (isset($api->download_link)) {
			$plugin['source'] = $api->download_link;
			$source =  $api->download_link;
		} else {
			echo 'Fail error (-2)';
			wp_die();
		}
		$nonce = 'install-plugin_' . $api->slug;
		/*
        $type = 'web';
        $url = $source;
        $title = 'wptools';
        */
		$plugin = $slug;
		// verbose...
		//    $upgrader = new Plugin_Upgrader($skin = new Plugin_Installer_Skin(compact('type', 'title', 'url', 'nonce', 'plugin', 'api')));
		class wpmemory_QuietSkin extends \WP_Upgrader_Skin
		{
			public function wpmemory_feedback($string, ...$args)
			{ /* no output */
			}
			public function wpmemory_header()
			{ /* no output */
			}
			public function wpmemory_footer()
			{ /* no output */
			}
		}
		$skin = new wpmemory_QuietSkin(array('api' => $api));
		$upgrader = new Plugin_Upgrader($skin);
		// var_dump($upgrader);
		try {
			$upgrader->install($source);
			//	get all plugins
			$all_plugins = get_plugins();
			// scan existing plugins
			foreach ($all_plugins as $key => $value) {
				// get full path to plugin MAIN file
				// folder and filename
				$plugin_file = $key;
				$slash_position = strpos($plugin_file, '/');
				$folder = substr($plugin_file, 0, $slash_position);
				// match FOLDER against SLUG
				// if matched then ACTIVATE it
				if ($slug == $folder) {
					/*
					// Activate
					$result = activate_plugin(ABSPATH . 'wp-content/plugins/' . $plugin_file);
					if (is_wp_error($result)) {
						// Process Error
						echo 'Fail error (-3)';
						wp_die();
					}
					*/
				} // if matched
			}
		} catch (Exception $e) {
			echo 'Fail error (-4)';
			wp_die();
		}
	} // activation
	echo 'OK';
	wp_die();
}


add_filter('plugin_row_meta', 'wpmemory_custom_plugin_row_meta', 10, 2);
function wpmemory_custom_plugin_row_meta($links, $file)
{
	if (strpos($file, 'wpmemory.php') !== false) {
		$new_links = array();

		$new_links['Pro'] = '<a href="https://wpmemory.com/premium/" target="_blank"><b><font color="#FF6600">Go Pro</font></b></a>';

		$links = array_merge($links, $new_links);
	}
	return $links;
}

function wpmemory_bill_go_pro_hide()
{
	// $today = date('Ymd', strtotime('+06 days'));
	$today = time();
	if (!update_option('bill_go_pro_hide', $today))
		add_option('bill_go_pro_hide', $today);
	wp_die();
}
add_action('wp_ajax_wpmemory_bill_go_pro_hide', 'wpmemory_bill_go_pro_hide');


/*

function wpmemory_localization_init()
{
	$path = basename( dirname( __FILE__ ) ) . '/language';
    $loaded = load_plugin_textdomain('wp-memory', false, $path);


    if (!$loaded and get_locale() <> 'en_US') {
        if (function_exists('wpmemory_localization_init_fail'))
            add_action('admin_notices', 'wpmemory_localization_init_fail');
    }
} 
*/







function wpmemory_localization_init()
{
	$path = WPMEMORYPATH . 'language/';
	$locale = apply_filters('plugin_locale', determine_locale(), 'wp-memory');

	// Full path of the specific translation file (e.g., es_AR.mo)
	$specific_translation_path = $path . "wp-memory-$locale.mo";
	$specific_translation_loaded = false;

	// Check if the specific translation file exists and try to load it
	if (file_exists($specific_translation_path)) {
		$specific_translation_loaded = load_textdomain('wp-memory', $specific_translation_path);
	}

	// List of languages that should have a fallback to a specific locale
	$fallback_locales = [
		'de' => 'de_DE',  // German
		'fr' => 'fr_FR',  // French
		'it' => 'it_IT',  // Italian
		'es' => 'es_ES',  // Spanish
		'pt' => 'pt_BR',  // Portuguese (fallback to Brazil)
		'nl' => 'nl_NL'   // Dutch (fallback to Netherlands)
	];

	// If the specific translation was not loaded, try to fallback to the generic version
	if (!$specific_translation_loaded) {
		$language = explode('_', $locale)[0];  // Get only the language code, ignoring the country (e.g., es from es_AR)

		if (array_key_exists($language, $fallback_locales)) {
			// Full path of the generic fallback translation file (e.g., es_ES.mo)
			$fallback_translation_path = $path . "wp-memory-{$fallback_locales[$language]}.mo";

			// Check if the fallback generic file exists and try to load it
			if (file_exists($fallback_translation_path)) {
				load_textdomain('wp-memory', $fallback_translation_path);
			}
		}
	}

	// Load the plugin
	load_plugin_textdomain('wp-memory', false, plugin_basename(WPMEMORYPATH) . '/language/');
}

function wpmemory_localization_init_fail()
{

	if (get_option('wpmemory_dismiss_language') == '1')
		return;

	echo '<div id="wpmemory_an2" class="update notice is-dismissible">
                     <br />
                     WP Memory Plugin not load the localization file (Language file).
                     <br />
                     Please, contact me at our Support Page to translate it on your language.
					 <br />
					 <br />
					 </div>';
}

function wpmemory_dismissible_notice2()
{
	$r = update_option('wpmemory_dismiss_language', '1');
	if (!$r) {
		$r = add_option('wpmemory_dismiss_language', '1');
	}
}
add_action('wp_ajax_wpmemory_dismissible_notice2', 'wpmemory_dismissible_notice2');

/*
function wpmemory_load_feedback()
{
  if (is_admin()) {
    // ob_start();
    // require_once(WPMEMORYPATH . "includes/feedback/feedback.php");
    require_once(WPMEMORYPATH . "includes/feedback/feedback-last.php");
	// ob_end_clean();
  }  
}
add_action('wp_loaded', 'wpmemory_load_feedback');
*/

// 2024
function wpmemory_tablexist($table)
{
	global $wpdb;
	//if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table)

	$table = esc_attr($table);
	$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
	// This query with prepare result in database error:
	// $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %i", $table ) );

	if ($table_exists == $table)
		return true;
	else
		return false;
}
function wpmemory_create_db_log()
{
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	$table = $wpdb->prefix . "wpmemory_log";
	$charset_collate = $wpdb->get_charset_collate();
	/*
    $sql = "CREATE TABLE " . $table . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `memory_usage` int(11) NOT NULL,
		`page` text NOT NULL,
        `flag` varchar(1) NOT NULL,
    UNIQUE (`id`)
    ) $charset_collate;";
	$result = dbDelta($sql);
	*/

	/*
	$sql = $wpdb->prepare(
		"CREATE TABLE %s (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`memory_usage` int(11) NOT NULL,
			`page` text NOT NULL,
			`flag` varchar(1) NOT NULL,
		UNIQUE (`id`)
		) %s;",
		$table,
		$charset_collate
	);
	*/



	$sql = "CREATE TABLE $table (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		`memory_usage` int(11) NOT NULL,
		`page` text NOT NULL,
		`flag` varchar(1) NOT NULL,
		UNIQUE (`id`)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

	return;
}
function wpmemory_add_log()
{
	global $wpdb;
	$table = $wpdb->prefix . "wpmemory_log";
	if (!wpmemory_tablexist($table))
		wpmemory_create_db_log();
	$current_page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$current_page = esc_url($current_page);
	if ($current_page !== null && $current_page !== '') {
		$current_page_path = trim($current_page, '/');
	} else {
		$current_page_path = '/';
	}
	$total_memory_usage = memory_get_usage();
	$total_memory_usage =  wpmemory_convert_to_bytes($total_memory_usage) . " ";
	if (empty($current_page_path))
		$current_page_path = '/';

	/*
	$sql = $wpdb->prepare(
		"INSERT INTO $table (date, memory_usage, page) VALUES (%s, %s, %s)",
		current_time('mysql'),
		$total_memory_usage,
		$current_page_path
	);
	*/



	$sql = $wpdb->prepare(
		"INSERT INTO %i (date, memory_usage, page) VALUES (%s, %s, %s)",
		$table,
		current_time('mysql'),
		$total_memory_usage,
		$current_page_path
	);





	if ($current_page_path == 'wp-admin/admin-ajax.php')
		return;
	// Execute a query
	$result = $wpdb->query($sql);
	// Verifique se a consulta foi bem-sucedida
	if ($result === false) {
		error_log('Error to insert in table: ' . $table . ' - Error  ' . $wpdb->last_error);
	}
}
function wpmemory_convert_to_bytes($value)
{
	$value = trim($value);
	$unit = strtoupper(substr($value, -1));
	$value = (int)$value;
	switch ($unit) {
		case 'G':
			$value *= 1024;
		case 'M':
			$value *= 1024;
		case 'K':
			$value *= 1024;
	}
	return $value;
}
wpmemory_add_log();

// clean log table
function wpmemory_keep_latest_records()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'wpmemory_log';
	// Get the total number of records
	$total_records = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
	//$total_records = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $table_name" ) );

	// If there are more than 100 records, delete the excess
	if ($total_records > 200) {
		$records_to_keep = 200;
		$records_to_delete = $total_records - $records_to_keep;
		//$wpdb->query("DELETE FROM $table_name ORDER BY id ASC LIMIT $records_to_delete");
		$wpdb->query($wpdb->prepare("DELETE FROM $table_name ORDER BY id ASC LIMIT %d", $records_to_delete));
	}
}
// Schedule the cron job hourly
if (!wp_next_scheduled('wpmemory_keep_latest_records_cron')) {
	wp_schedule_event(time(), 'hourly', 'wpmemory_keep_latest_records_cron');
}
// Hook the function to the cron job
add_action('wpmemory_keep_latest_records_cron', 'wpmemory_keep_latest_records');


// ---------------------------------

function wpmemory_bill_hooking_diagnose()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			$declared_classes = get_declared_classes();
			foreach ($declared_classes as $class_name) {
				if (strpos($class_name, "Bill_Diagnose") !== false) {
					return;
				}
			}
			$plugin_slug = 'wpmemory';
			$plugin_text_domain = $plugin_slug;
			$notification_url = "https://wpmemory.com/fix-low-memory-limit/";
			$notification_url2 =
				"https://wptoolsplugin.com/site-language-error-can-crash-your-site/";
			require_once dirname(__FILE__) . "/includes/diagnose/class_bill_diagnose.php";
		}
	}
}
add_action("plugins_loaded", "wpmemory_bill_hooking_diagnose", 10);


function wpmemory_bill_hooking_catch_errors()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			$declared_classes = get_declared_classes();
			foreach ($declared_classes as $class_name) {
				if (strpos($class_name, "bill_catch_errors") !== false) {
					return;
				}
			}
			$wpmemory_plugin_slug = 'restore-classic-widgets';
			require_once dirname(__FILE__) . "/includes/catch-errors/class_bill_catch_errors.php";
		}
	}
}
add_action("init", "wpmemory_bill_hooking_catch_errors", 15);



function wpmemory_bill_more()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			$declared_classes = get_declared_classes();
			foreach ($declared_classes as $class_name) {
				if (strpos($class_name, "Bill_show_more_plugins") !== false) {
					// return;
				}
			}
			require_once dirname(__FILE__) . "/includes/more-tools/class_bill_more.php";
		}
	}
}
add_action("init", "wpmemory_bill_more");




//debug2();

function wpmemory_load_feedback()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			// ob_start();
			//debug2();
			require_once dirname(__FILE__) . "/includes/feedback-last/feedback-last.php";
			// ob_end_clean();
		}
	}
}
add_action('wp_loaded', 'wpmemory_load_feedback');
