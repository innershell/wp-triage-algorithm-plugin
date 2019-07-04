<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// main model containing general config and UI functions
class ChainedQuiz {

	// Function that is called when the plugin is activated.
   	static function install($update = false) {
		global $wpdb;	
		$wpdb -> show_errors();
		
		// If new activation, run the initiatlize some variables first.
		if(!$update) self::init();
		
		// CHAINED_QUIZZES
		if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_QUIZZES."'") != CHAINED_QUIZZES) {        
			$sql = "CREATE TABLE `" . CHAINED_QUIZZES . "` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`title` VARCHAR(255) NOT NULL DEFAULT '',
					`output` TEXT,
					`email_admin` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`email_user` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`require_login` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`times_to_take` INT UNSIGNED NOT NULL DEFAULT 0,
					`save_source_url` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`set_email_output` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`email_output` TEXT
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
		}
		
		// CHAINED_QUESTIONS
		if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_QUESTIONS."'") != CHAINED_QUESTIONS) {        
			$sql = "CREATE TABLE `" . CHAINED_QUESTIONS . "` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`title` VARCHAR(255) NOT NULL DEFAULT '',
					`question` TEXT,
					`qtype` VARCHAR(20) NOT NULL DEFAULT '',
					`soap_type` VARCHAR(1) DEFAULT '',
					`rank` INT UNSIGNED NOT NULL DEFAULT 0,
					`autocontinue` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
					`accept_comments` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`accept_comments_label` VARCHAR(255) NOT NULL DEFAULT ''
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
		} 
		
		// CHAINED_CHOICES
		if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_CHOICES."'") != CHAINED_CHOICES) {        
			$sql = "CREATE TABLE `" . CHAINED_CHOICES . "` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`question_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`choice` TEXT,
					`provider_note` TEXT,
					`assessment` TEXT,
					`plan` TEXT,
					`points` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					`is_correct` TINYINT UNSIGNED NOT NULL DEFAULT 0,
					`goto` VARCHAR(100) NOT NULL DEFAULT 'next'
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
		} 
		
		// CHAINED_RESUTS
		if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_RESULTS."'") != CHAINED_RESULTS) {        
			$sql = "CREATE TABLE `" . CHAINED_RESULTS . "` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`points_bottom` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					`points_top` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					`title` VARCHAR(255) NOT NULL DEFAULT '',
					`description` TEXT,
					`redirect_url` VARCHAR(255) NOT NULL DEFAULT ''
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
		} 
		
		// CHAINED_COMPLETED	
		if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_COMPLETED."'") != CHAINED_COMPLETED) {        
			$sql = "CREATE TABLE `" . CHAINED_COMPLETED . "` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`points` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					`result_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`datetime` DATETIME,
					`ip` VARCHAR(20) NOT NULL DEFAULT '',
					`user_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`snapshot` TEXT,
					`not_empty` TINYINT NOT NULL DEFAULT 0,
					`source_url` VARCHAR(255) NOT NULL DEFAULT '',
					`email` VARCHAR(255) NOT NULL DEFAULT ''
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
		} 	 
		
		// CHAINED_USER_ANSWERS
		if($wpdb->get_var("SHOW TABLES LIKE '".CHAINED_USER_ANSWERS."'") != CHAINED_USER_ANSWERS) {        
			$sql = "CREATE TABLE `" . CHAINED_USER_ANSWERS . "` (
					`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`quiz_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`completion_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`question_id` INT UNSIGNED NOT NULL DEFAULT 0,
					`answer` TEXT,
					`answer_text` TEXT,
					`points` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					`comments` TEXT
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
		} 	 
		
		/**
		 * Alter the old tables to include new columns from newer plugin versions.
		 */
		// chainedquiz_add_db_fields(array(
		// 	array("name" => 'autocontinue', 'type' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0'),
		// 	array("name" => 'sort_order', 'type' => 'INT UNSIGNED NOT NULL DEFAULT 0'),
		// 	array("name" => 'accept_comments', 'type' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0'),
		// 	array("name" => 'accept_comments_label', 'type' => "VARCHAR(255) NOT NULL DEFAULT ''"),
		// ), CHAINED_QUESTIONS);
		
		// chainedquiz_add_db_fields(array(
		// 	array("name" => 'redirect_url', 'type' => "VARCHAR(255) NOT NULL DEFAULT ''"),
		// ), CHAINED_RESULTS);
		
		// chainedquiz_add_db_fields(array(
		// 	array("name" => 'comments', 'type' => "TEXT"),
		// ), CHAINED_USER_ANSWERS);
		
		// chainedquiz_add_db_fields(array(
		// 	array("name" => 'email_admin', 'type' => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		// 	array("name" => 'email_user', 'type' => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		// 	array("name" => 'require_login', 'type' => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		// 	array("name" => 'times_to_take', 'type' => "INT UNSIGNED NOT NULL DEFAULT 0"),
		// 	array("name" => 'save_source_url', 'type' => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		// 	array("name" => 'set_email_output', 'type' => "TINYINT UNSIGNED NOT NULL DEFAULT 0"),
		// 	array("name" => 'email_output', 'type' => "TEXT"),
		// ), CHAINED_QUIZZES);
		
		// chainedquiz_add_db_fields(array(
		// 	array("name" => 'not_empty', 'type' => "TINYINT NOT NULL DEFAULT 0"), /*When initially creating a record, it is empty. If it remains so we have to delete it.*/
		// 	array("name" => 'source_url', 'type' => "VARCHAR(255) NOT NULL DEFAULT ''"), /* Page where the quiz is published */ 
		// 	array("name" => 'email', 'type' => "VARCHAR(255) NOT NULL DEFAULT ''"), /* email of non-logged in users when required */
		// ), CHAINED_COMPLETED);
		
		// fix sort order once for old quizzes (in version 0.7.5)
		// if(get_option('chained_fixed_sort_order') != 1) {
		// 	ChainedQuizQuestions :: fix_sort_order_global();
		// 	update_option('chained_fixed_sort_order', 1);
		// }
		
		// update not_empty = 1 for all completed records prior to version 0.8.7 and DB version 0.66
		// $version = get_option('chained_version');
		// if($version < 0.67) {
		// 	$wpdb->query("UPDATE ".CHAINED_COMPLETED." SET not_empty=1");
		// }
		
		// setup the default options (when not yet saved ever)
		if(get_option('chained_sender_name') == '') {
			update_option('chained_sender_name', __('WordPress Admin', 'chained'));
			update_option('chained_sender_email', get_option('admin_email'));
			update_option('chained_admin_subject', __('User results on {{quiz-name}}', 'chained'));
			update_option('chained_user_subject', __('Your results on {{quiz-name}}', 'chained'));
		}

		$current_version = get_option('chained_version');

		/** PERFORM VERSION-SPECIFIC UPGRADES HERE **/
		if ($current_version < '2.2') {
			update_option('chained_delete_data', 'no');
		}

		// Set the current plugin version number.
		update_option('chained_version', "2.2");
		// exit;
	}
   
   // main menu
   static function menu() {
   	$chained_caps = current_user_can('manage_options') ? 'manage_options' : 'chained_manage';
   	
   	add_menu_page(__('Triage Algorithm', 'chained'), __('Triage Algorithm', 'chained'), $chained_caps, "chained_quizzes", array('ChainedQuizQuizzes', "manage"));
   	add_submenu_page('chained_quizzes', __('Algorithms', 'chained'), __('Algorithms', 'chained'), $chained_caps, 'chained_quizzes', array('ChainedQuizQuizzes', "manage"));					
   	add_submenu_page('chained_quizzes', __('Settings', 'chained'), __('Settings', 'chained'), 'manage_options', 'chainedquiz_options', array('ChainedQuiz','options'));				
   	add_submenu_page('chained_quizzes', __('Social Sharing', 'chained'), __('Social Sharing', 'chained'), $chained_caps, 'chainedquiz_social_sharing', array('ChainedSharing','options'));				
   		
   	add_submenu_page(NULL, __('Chained Quiz Results', 'chained'), __('Chained Quiz Results', 'chained'), $chained_caps, 'chainedquiz_results', array('ChainedQuizResults','manage'));	
   	add_submenu_page(NULL, __('Chained Quiz Questions', 'chained'), __('Chained Quiz Questions', 'chained'), $chained_caps, 'chainedquiz_questions', array('ChainedQuizQuestions','manage'));	
   	add_submenu_page(NULL, __('Users Completed Quiz', 'chained'), __('Users Completed Quiz', 'chained'), $chained_caps, 'chainedquiz_list', array('ChainedQuizCompleted','manage'));		
   	
	}
	
	// CSS and JS
	static function scripts() {
		// CSS
		wp_register_style( 'chained-css', CHAINED_URL.'css/main.css?ver=1.0.7');
		wp_enqueue_style( 'chained-css' );
   
   		wp_enqueue_script('jquery');
	   
	   // Chained quiz's own Javascript
		wp_register_script(
				'chained-common',
				CHAINED_URL.'js/common.js',
				false,
				'2.2',
				false
		);
		wp_enqueue_script("chained-common");
		
		$translation_array = array('please_answer' => __('Please answer the question', 'chained'),);
		wp_localize_script( 'chained-common', 'chained_i18n', $translation_array );	
	}
	
	// initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'chained', false, CHAINED_RELATIVE_PATH."/languages/" );
		if (!session_id()) @session_start();
		
		// define table names 
		define( 'CHAINED_QUIZZES', $wpdb->prefix. "chained_quizzes");
		define( 'CHAINED_QUESTIONS', $wpdb->prefix. "chained_questions");
		define( 'CHAINED_CHOICES', $wpdb->prefix. "chained_choices");
		define( 'CHAINED_RESULTS', $wpdb->prefix. "chained_results");
		define( 'CHAINED_COMPLETED', $wpdb->prefix. "chained_completed");
		define( 'CHAINED_USER_ANSWERS', $wpdb->prefix. "chained_user_answers");
		define( 'CHAINED_VERSION', get_option('chained_version'));
				
		// shortcodes
		add_shortcode('triage-algorithm', array("TriageShortcodes", "algorithmShortcodeHandler"));
		add_shortcode('triage-submissions', array("TriageShortcodes", "responsesShortcodeHandler"));
		add_shortcode('chained-share', array("ChainedSharing", "display"));		
		
		// once daily delete empty records older than 1 day
		// if(get_option('chainedquiz_cleanup') != date("Y-m-d") and defined('CHAINED_COMPLETED')) {
		// 	$wpdb->query("DELETE FROM ".CHAINED_COMPLETED." WHERE not_empty=0 AND datetime < '".current_time('mysql')."' - INTERVAL 24 HOUR");
		// 	update_option('chainedquiz_cleanup', date("Y-m-d"));
		// }
		
		add_action('template_redirect', array('ChainedSharing', 'social_share_snippet'));
		
		// default CSV separator if not set
		if(get_option('chained_csv_delim') == '') {
			update_option('chained_csv_delim', ',');
			update_option('chained_csv_quotes', '1');
		}
				
		// $version = get_option('chained_version');
		// if($version < '0.8') self::install(true);

		// Go ahead and activate the plugin now by running the install script.
		self::install(true);
	}
			
	// manage general options
	static function options() {
		global $wpdb, $wp_roles;
		$roles = $wp_roles->roles;		
		
		if(!empty($_POST['ok']) and check_admin_referer('chained_options')) {
			// sender's email and email subjects
			update_option('chained_sender_name', sanitize_text_field($_POST['sender_name']));
			update_option('chained_sender_email', sanitize_email($_POST['sender_email']));
			update_option('chained_user_subject', sanitize_text_field($_POST['user_subject']));						
			update_option('chained_admin_subject', sanitize_text_field($_POST['admin_subject']));
			update_option('chained_admin_emails', sanitize_text_field($_POST['admin_emails']));
			update_option('chained_csv_delim', sanitize_text_field($_POST['csv_delim']));
			$_POST['csv_quotes'] = empty($_POST['csv_quotes']) ? 0 : 1;
			update_option('chained_csv_quotes', $_POST['csv_quotes']);
			
			// user interface options
			$hide_go_ahead = empty($_POST['hide_go_ahead']) ? 0 : 1;
			$ui = array('hide_go_ahead' => $hide_go_ahead);			
			update_option('chained_ui', $ui);			
			
			if(current_user_can('manage_options')) {
				foreach($roles as $key=>$role) {
					$r=get_role($key);
					
					if(@in_array($key, $_POST['manage_roles'])) {					
	    				if(!$r->has_cap('chained_manage')) $r->add_cap('chained_manage');
					}
					else $r->remove_cap('chained_manage');
				}
			}
		}	
		
		$ui = get_option('chained_ui');
		$delim = get_option('chained_csv_delim');
		   	
		require(CHAINED_PATH."/views/options.html.php");
	}	
	
	static function help() {
		require(CHAINED_PATH."/views/help.php");
	}	
}