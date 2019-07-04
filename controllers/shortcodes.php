<?php 
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizShortcodes {
	static function quiz($atts) {
		global $wpdb;
		$quiz_id = @$atts[0];
		if(empty($quiz_id) or !is_numeric($quiz_id)) return __('No quiz to load', 'chained');
		ob_start();
		ChainedQuizQuizzes :: display($quiz_id);
		$content = ob_get_clean();
		return $content;
	} // end quiz()
}