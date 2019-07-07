<?php 
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TriageShortcodes {
	// Shortcode handler/receiver for [triage-topic].
	static function topicShortcodeHandler($atts) {
		global $wpdb;
		$quiz_id = @$atts[0];
		if(empty($quiz_id) or !is_numeric($quiz_id)) return __('No quiz to load', 'chained');
		ob_start();
		ChainedQuizQuizzes :: display($quiz_id);
		$content = ob_get_clean();
		return $content;
	} // end topic()

	// Shortcode handler/receiver for [triage-submissions].
	static function responsesShortcodeHandler($atts) {

		$args = shortcode_atts( array(
			'topic' => '0'
		), $atts );

		$topic_id = @$atts[0];
		ob_start();
		ChainedQuizCompleted :: view_submissions($args['topic']);
		$content = ob_get_clean();
		return $content;
	} // end dashboard()
}
