<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// handle all ajax
function chainedquiz_ajax() {
	
	// If there is no action specified for this AJAX call, then ABORT.
	$action = empty($_POST['chainedquiz_action']) ? 'ABORT' : $_POST['chainedquiz_action'];
	if(in_array($action, array('ABORT'))) exit; 
	
	switch($action) {
		// answer a question or quiz
		case 'answer':
			echo ChainedQuizQuizzes :: answer_question();
			break;
		case 'feedback':
			$comment = $_POST['comment'];
			echo ChainedQuizCompleted :: feedback($comment);
			break;
	}

	exit;
}