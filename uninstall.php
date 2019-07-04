<?php
global $wpdb;

if(!defined('WP_UNINSTALL_PLUGIN') or !WP_UNINSTALL_PLUGIN) exit;
$delete_data = get_option ('chained_delete_data');

if ($delete_data == 'yes') {

    // Drop plugin tables.
    $wpdb->query(sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'chained_choices'));
    $wpdb->query(sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'chained_completed'));
    $wpdb->query(sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'chained_questions'));
    $wpdb->query(sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'chained_quizzes'));
    $wpdb->query(sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'chained_results'));
    $wpdb->query(sprintf( "DROP TABLE IF EXISTS %s", $wpdb->prefix . 'chained_user_answers'));
        
    // clean options
    delete_option('chained_csv_delim');
    delete_option('chained_csv_quotes');
    delete_option('chained_fixed_sort_order');
    delete_option('chained_sender_name');
    delete_option('chained_sender_email');
    delete_option('chained_admin_subject');
    delete_option('chained_user_subject');
    delete_option('chained_admin_emails');
    delete_option('chained_ui');
    delete_option('chained_version');
    delete_option('chained_delete_data');
    delete_option('chainedquiz_cleanup');

}