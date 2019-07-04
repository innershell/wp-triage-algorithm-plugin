<?php 
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TriageShortcodes {
	// Shortcode handler/receiver for [triage-algorithm].
	static function algorithmShortcodeHandler($atts) {
		global $wpdb;
		$quiz_id = @$atts[0];
		if(empty($quiz_id) or !is_numeric($quiz_id)) return __('No quiz to load', 'chained');
		ob_start();
		ChainedQuizQuizzes :: display($quiz_id);
		$content = ob_get_clean();
		return $content;
	} // end algorithm()

	// Shortcode handler/receiver for [triage-dashboard].
	static function responsesShortcodeHandler($atts) {
		ob_start();
		ChainedQuizCompleted :: view_responses();
		$content = ob_get_clean();
		return $content;
	} // end dashboard()
}
