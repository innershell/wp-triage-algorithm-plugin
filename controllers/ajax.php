<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// handle all ajax
function chainedquiz_ajax() {
	$action = empty($_POST['chainedquiz_action']) ? 'answer' : $_POST['chainedquiz_action'];
	
	// currently just "answer" but the code will handle future versions
	if(!in_array($action, array('answer'))) exit; 
	
	switch($action) {
		// answer a question or quiz
		case 'answer':
		default:
			echo ChainedQuizQuizzes :: answer_question();
		break;
	}

	exit;
}