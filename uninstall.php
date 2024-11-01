<?php
/**
 * @author William Sergio Minossi
 * @copyright 2016
 */

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Array of options to be deleted
$wpmemory_options = array(
    'wpmemory_activated_pointer',
    'wpmemory_php_memory_limit',
    'wpmemory_activated_notice',
    'wpmemory_was_activated',
    'wp_memory_update',
    'wpmemory_dismiss_language',
    'wpmemory_last_notification_date',
    'wpmemory_last_notification_date2'
);

// Delete options
foreach ($wpmemory_options as $option_name) {
    if (is_multisite()) {
        // Delete the option from the site in a multisite installation
        delete_site_option($option_name);
    } else {
        // Delete the option from a single site
        delete_option($option_name);
    }
}

// Drop a custom db table
global $wpdb;
$table = $wpdb->prefix . "wpmemory_log";
$wpdb->query( "DROP TABLE IF EXISTS $table" );

// Clean up scheduled cron jobs
wp_clear_scheduled_hook('wpmemory_keep_latest_records_cron');
?>
