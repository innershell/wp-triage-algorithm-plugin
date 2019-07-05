<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// main model containing general config and UI functions
class ChainedQuiz {

	// Function that is called when the plugin is activated.
   	static function install($update = false) {
		global $wpdb;	
		$wpdb -> show_errors();
		
		// If new activation, initialize some variables first.
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
					`subjective` TEXT,
					`objective` TEXT,
					`assessment` TEXT,
					`plan` TEXT,
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
		
		// Setup the default options (when not yet saved ever)
		if (get_option('chained_sender_name') == '') {
			update_option('chained_sender_name', __('WordPress Admin', 'chained'));
			update_option('chained_sender_email', get_option('admin_email'));
			update_option('chained_admin_subject', __('User results on {{quiz-name}}', 'chained'));
			update_option('chained_user_subject', __('Your results on {{quiz-name}}', 'chained'));
		}

		$current_version = get_option('chained_version');
		error_log("Current 'chained_version' = ".$current_version);

		/** PERFORM VERSION-SPECIFIC UPGRADES HERE **/
		if ($current_version < '2.2') {
			update_option('chained_delete_data', 'no');
		} elseif ($current_version < '4.0') {
			update_option('chained_debug_mode', 'off');
		} elseif ($current_version < '5.0') {
			$wpdb->query("ALTER TABLE ".CHAINED_RESULTS." ADD COLUMN subjective TEXT AFTER description;");
			$wpdb->query("ALTER TABLE ".CHAINED_RESULTS." ADD COLUMN objective TEXT AFTER subjective;");
			$wpdb->query("ALTER TABLE ".CHAINED_RESULTS." ADD COLUMN assessment TEXT AFTER objective;");
			$wpdb->query("ALTER TABLE ".CHAINED_RESULTS." ADD COLUMN plan TEXT AFTER assessment;");
		}

		// Set the current plugin version number.
		update_option('chained_version', '5.1');
		// exit;
	}
   
	// main menu
	static function menu() {
		$chained_caps = current_user_can('manage_options') ? 'manage_options' : 'chained_manage';
		
		add_menu_page(__('Triage Algorithm', 'chained'), __('Triage Algorithm', 'chained'), $chained_caps, "chained_quizzes", array('ChainedQuizQuizzes', "manage"));
		add_submenu_page('chained_quizzes', __('Algorithms', 'chained'), __('Algorithms', 'chained'), $chained_caps, 'chained_quizzes', array('ChainedQuizQuizzes', "manage"));					
		add_submenu_page('chained_quizzes', __('Settings', 'chained'), __('Settings', 'chained'), 'manage_options', 'chainedquiz_options', array('ChainedQuiz','options'));
		add_submenu_page('chained_quizzes', __('Social Sharing', 'chained'), __('Social Sharing', 'chained'), $chained_caps, 'chainedquiz_social_sharing', array('ChainedSharing','options'));				
		add_submenu_page('chained_quizzes', __('Help', 'chained'), __('Help', 'chained'), $chained_caps, 'chainedquiz_help', array('ChainedQuiz','help'));
			
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
				'5.1',
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
		
		// Define table names as named constants.
		define( 'CHAINED_QUIZZES', $wpdb->prefix. "chained_quizzes");
		define( 'CHAINED_QUESTIONS', $wpdb->prefix. "chained_questions");
		define( 'CHAINED_CHOICES', $wpdb->prefix. "chained_choices");
		define( 'CHAINED_RESULTS', $wpdb->prefix. "chained_results");
		define( 'CHAINED_COMPLETED', $wpdb->prefix. "chained_completed");
		define( 'CHAINED_USER_ANSWERS', $wpdb->prefix. "chained_user_answers");
		//define( 'CHAINED_VERSION', get_option('chained_version'));
				
		// Register shortcodes offered by this plugin.
		add_shortcode('triage-algorithm', array("TriageShortcodes", "algorithmShortcodeHandler"));
		add_shortcode('triage-submissions', array("TriageShortcodes", "responsesShortcodeHandler"));
		add_shortcode('chained-share', array("ChainedSharing", "display"));		
		
		add_action('template_redirect', array('ChainedSharing', 'social_share_snippet'));
		
		// default CSV separator if not set
		if (get_option('chained_csv_delim') == '') {
			update_option('chained_csv_delim', ',');
			update_option('chained_csv_quotes', '1');
		}
				
		// Go ahead and activate the plugin now by running the install script.
		self::install(true);
	}
			
	// manage general options
	static function options() {
		global $wpdb, $wp_roles;
		$roles = $wp_roles->roles;		
		
		if(!empty($_POST['ok']) and check_admin_referer('chained_options')) {
			// Roles
			if(current_user_can('manage_options')) {
				foreach($roles as $key=>$role) {
					$r=get_role($key);
					
					if(@in_array($key, $_POST['manage_roles'])) {					
						if(!$r->has_cap('chained_manage')) $r->add_cap('chained_manage');
					}
					else $r->remove_cap('chained_manage');
				}
			}
			
			// Email Options
			update_option('chained_sender_name', sanitize_text_field($_POST['sender_name']));
			update_option('chained_sender_email', sanitize_email($_POST['sender_email']));
			update_option('chained_user_subject', sanitize_text_field($_POST['user_subject']));						
			update_option('chained_admin_subject', sanitize_text_field($_POST['admin_subject']));
			update_option('chained_admin_emails', sanitize_text_field($_POST['admin_emails']));
			update_option('chained_csv_delim', sanitize_text_field($_POST['csv_delim']));
			$_POST['csv_quotes'] = empty($_POST['csv_quotes']) ? 0 : 1;
			update_option('chained_csv_quotes', $_POST['csv_quotes']);
			
			// User Interface Options
			$hide_go_ahead = empty($_POST['hide_go_ahead']) ? 0 : 1;
			$ui = array('hide_go_ahead' => $hide_go_ahead);			
			update_option('chained_ui', $ui);

			//CSV Exports
			$ui = get_option('chained_ui');
			$delim = get_option('chained_csv_delim');
				
			// Uninstall settings
			/** PLACEHOLDER FOR FUTURE CODE */

			// Debug Mode
			update_option('chained_debug_mode', sanitize_text_field($_POST['debug_mode']));
		}	
		

		require(CHAINED_PATH."/views/options.html.php");
	}	
	
	static function help() {
		require(CHAINED_PATH."/views/help.html.php");
	}	
}