<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizQuestions {
	static function manage() {
 		$action = empty($_GET['action']) ? 'list' : $_GET['action']; 
		switch($action) {
			case 'add':
				self :: add_question();
			break;
			case 'edit': 
				self :: edit_question();
			break;
			case 'list':
			default:
				self :: list_questions();	 
			break;
		}
	} // end manage()
	
	static function add_question() {
		global $wpdb;
		$_question = new ChainedQuizQuestion();
		
		if(!empty($_POST['ok']) and check_admin_referer('chained_question')) {
			try {
				$_POST['quiz_id'] = intval($_GET['quiz_id']);
				$qid = $_question->add($_POST);		
				$_question->save_choices($_POST, $qid);	
				chained_redirect("admin.php?page=chainedquiz_questions&quiz_id=".intval($_GET['quiz_id']));
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", intval($_GET['quiz_id'])));
		
		// select other questions for the go-to dropdown
		$other_questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d ORDER BY title", $quiz->id));
		
		include(CHAINED_PATH.'/views/question.html.php');
	} // end add_question
	
	static function edit_question() {
		global $wpdb;
		$_question = new ChainedQuizQuestion();
		
		if(!empty($_POST['ok']) and check_admin_referer('chained_question')) {
			try {
				$_question->save($_POST, $_GET['id']);
				$_question->save_choices($_POST, $_GET['id']);
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		// select the quiz and question		
		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE id=%d", intval($_GET['id'])));
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $question->quiz_id));

		// select question choices
		$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." WHERE question_id=%d ORDER BY id ", $question->id));	
		
		// select other questions for the go-to dropdown
		$other_questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." 
			WHERE quiz_id=%d AND id!=%d ORDER BY title", $quiz->id, $question->id));	
		
		include(CHAINED_PATH.'/views/question.html.php');
	} // end edit_quiz
	
	// list and delete questions
	static function list_questions() {
		global $wpdb;
		$_question = new ChainedQuizQuestion();
		
		if(!empty($_GET['del'])) {
			$_question->delete($_GET['id']);			
		}
		
		if(!empty($_GET['move'])) {
			// select question
			$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE id=%d", intval($_GET['move'])));
			
			if($_GET['dir'] == 'up') {
				$new_order = $question->sort_order - 1;
				if($new_order < 0) $new_order = 0;
				
				// shift others
				$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUESTIONS." SET sort_order=sort_order+1 
				  WHERE id!=%d AND sort_order=%d AND quiz_id=%d", intval($_GET['move']), $new_order, intval($_GET['quiz_id'])));
			}
			else {
				$new_order = $question->sort_order+1;			
	
				// shift others
				$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUESTIONS." SET sort_order=sort_order-1 
	  				WHERE id!=%d AND sort_order=%d AND quiz_id=%d", intval($_GET['move']), $new_order, intval($_GET['quiz_id'])));
			}
			
			// change this one
			$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUESTIONS." SET sort_order=%d WHERE id=%d", 
				$new_order, intval($_GET['move'])));
				
			// redirect 	
			chained_redirect('admin.php?page=chainedquiz_questions&quiz_id=' . intval($_GET['quiz_id']));
		}
		
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", intval($_GET['quiz_id'])));
		$questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d ORDER BY sort_order, id", intval($_GET['quiz_id'])));
		$count = count($questions);
		include(CHAINED_PATH."/views/questions.html.php");
	} // end list_quizzes
	
	// initially fix sort order of the questions in all quizzes
	// it sets order based on question ID
	static function fix_sort_order_global() {
		global $wpdb;
		
		$quizzes = $wpdb->get_results("SELECT id FROM ".CHAINED_QUIZZES);
		
		foreach($quizzes as $quiz) {
			$min_id = $wpdb->get_var($wpdb->prepare("SELECT MIN(id) FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d", $quiz->id));
			$min_id--;
			
			$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUESTIONS." SET
				sort_order = id - %d WHERE quiz_id=%d", $min_id, $quiz->id));
		}
		
	}	// end fix_sort_order_global
}