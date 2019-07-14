<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizQuizzes {
	static function manage() {
 		$action = empty($_GET['action']) ? 'list' : $_GET['action']; 
		switch($action) {
			case 'add':
				self :: add_quiz();
			break;
			case 'edit': 
				self :: edit_quiz();
			break;
			case 'list':
			default:
				self :: list_quizzes();	 
			break;
		}
	} // end manage()
	
	static function add_quiz() {
		$_quiz = new ChainedQuizQuiz();
		
		if(!empty($_POST['ok']) and check_admin_referer('chained_quiz')) {
			try {
				$qid = $_quiz->add($_POST);   
				chained_redirect("admin.php?page=chainedquiz_results&quiz_id=".$qid);
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		$output = __('Success: Topic Completed
					{{soap-note}}', 'chained');
		$is_published = false;
		include(CHAINED_PATH.'/views/quiz.html.php');
	} // end add_quiz
	
	static function edit_quiz() {
		global $wpdb;
		$_quiz = new ChainedQuizQuiz();
		
		if(!empty($_POST['ok']) and check_admin_referer('chained_quiz')) {
			try {
				$_quiz->save($_POST, $_GET['id']);			
				chained_redirect("admin.php?page=chained_quizzes");
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		// select the quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", intval($_GET['id'])));
	   $output = stripslashes($quiz->output); 
	   
	   // is this quiz currently published?
		$is_published = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%[triage-topic ".intval($_GET['id'])."]%' 
				AND post_status='publish' AND post_title!=''");	
		include(CHAINED_PATH.'/views/quiz.html.php');
	} // end edit_quiz
	
	// list and delete quizzes
	static function list_quizzes() {
		global $wpdb;
		$_quiz = new ChainedQuizQuiz();
		
		if(!empty($_GET['del'])) {
			$_quiz->delete($_GET['id']);
			chained_redirect("admin.php?page=chained_quizzes");
		}
		
		if(!empty($_GET['copy'])) {
		   $_quiz->copy($_GET['id']);
		   chained_redirect("admin.php?page=chained_quizzes");
		}

		if(!empty($_GET['export'])) {			
			$now = gmdate('D, d M Y H:i:s') . ' GMT';	
			$filename = 'quiz-results.sql';	
			header('Content-Type: ' . kiboko_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Pragma: no-cache');

			$crlf = kiboko_define_newline();
			//$prefix = "wp_";
			$prefix = empty($_GET['prefix']) ? "wp_" : $_GET['prefix'];
			
			// Generate SQL for WP_CHAINED_QUIZZES table.
			$quizzes = $wpdb->get_results('SELECT * FROM ' . CHAINED_QUIZZES);
			echo "-- $crlf";
			echo "-- Generated Time: $now $crlf";
			echo "-- $crlf";
			echo 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";' . $crlf;
			echo "   $crlf";
			echo "-- $crlf";
			echo '-- Truncate and insert CHAINED_QUIZZES.' . $crlf;
			echo "-- $crlf";
			echo "TRUNCATE TABLE " . $prefix . "chained_quizzes; $crlf";
			echo "INSERT INTO " . $prefix . "chained_quizzes (id, title, output, email_admin, email_user, require_login, times_to_take, save_source_url, set_email_output, email_output) VALUES $crlf";
			$i = count($quizzes);
			foreach ($quizzes as $quiz) {
				$i--;
				echo "($quiz->id, '$quiz->title', '" . str_replace("\r\n", "\\r\\n", $quiz->output) . "', $quiz->email_admin, $quiz->email_user, $quiz->require_login, $quiz->times_to_take, $quiz->save_source_url, $quiz->set_email_output, '" . str_replace("\r\n", "\\r\\n", $quiz->email_output) . "')";
				echo $i > 0 ? ",$crlf" : ";$crlf";
			}

			// Generate SQL for CHAINED_QUESTIONS data.

			// Generate SQL for CHAINED_CHOICES data.

			// Generate SQL for CHAINED_RESULTS data.



			exit;
		 }
		
		// select quizzes
		$quizzes = $wpdb->get_results("SELECT tQ.*, COUNT(tC.id) as submissions 
			FROM ".CHAINED_QUIZZES." tQ LEFT JOIN ".CHAINED_COMPLETED." tC ON tC.quiz_id = tQ.id AND tC.not_empty=1
			GROUP BY tQ.id ORDER BY tQ.title ASC");
		
		// now select all posts that have watu shortcode in them
		$posts=$wpdb->get_results("SELECT * FROM {$wpdb->posts} 
		WHERE post_content LIKE '%[triage-topic %]%' AND post_title!=''
		AND post_status='publish' ORDER BY post_date DESC");	
		
		// match posts to exams
		foreach($quizzes as $cnt=>$quiz) {
			foreach($posts as $post) {
				if(strstr($post->post_content,"[triage-topic ".$quiz->id."]")) {
					$quizzes[$cnt]->post=$post;			
					break;
				}
			}
		}
		include(CHAINED_PATH."/views/chained-quizzes.html.php");
	} // end list_quizzes	
	
	// displays a quiz
	static function display($quiz_id) {
	   global $wpdb, $user_ID, $post;
	   $_question = new ChainedQuizQuestion();
	   
	   // select the quiz
	   $quiz = $wpdb -> get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $quiz_id));
	   if(empty($quiz->id)) die(__('Quiz not found', 'chained'));
	   
	   // completion ID already created?
		if(empty($_SESSION['chained_completion_id'])) {			
			$wpdb->query( $wpdb->prepare("INSERT INTO ".CHAINED_COMPLETED." SET
		 		quiz_id = %d, datetime = NOW(), ip = %s, user_id = %d",
		 		$quiz->id, $_SERVER['REMOTE_ADDR'], $user_ID));
		 	$_SESSION['chained_completion_id'] = $wpdb->insert_id;	
		}
	   
		 // select the first question
		 $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d
		 	ORDER BY sort_order, id LIMIT 1", $quiz->id));
		 if(empty($question->id)) {
		 	 _e('This quiz has no questions.', 'chained');
		 	 return false;
		 }	
		 
		 // select possible answers
		 $choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." 
		 	WHERE quiz_id=%d AND question_id=%d ORDER BY id", $quiz->id, $question->id));
		 			 	
		 $first_load = true;			 	
		 include(CHAINED_PATH."/views/display-quiz.html.php");
	}

	/**************************************************************************
	 * FUNCTION: Answer the question or complete the quiz.
	 **************************************************************************/
	static function answer_question() {
		global $wpdb, $user_ID;
		$_quiz = new ChainedQuizQuiz();
		$_question = new ChainedQuizQuestion();
		
		$post = get_post($_POST['post_id']);
		
		// select quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", intval($_POST['quiz_id'])));
		
		// select question
		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE id=%d", intval($_POST['question_id'])));
		
		// prepare $answer var		
		$answer = '';
		$answer_text = '';

		switch($question->qtype) {
			case 'text':
				$answer = @$_POST['answers'];
				$answer_text = @$_POST['answer_texts'];
				break;
			case 'date':
				$answer = @$_POST['answers'];
				$answer_text = @$_POST['answer_texts'];
				break;
			case 'checkbox':
				$answer = @$_POST['answers'];
				break;
			case 'radio':
				$answer = @$_POST['answer'];
					break;
		}

		// Check to make sure answer is provided.
		if(empty($answer)) $answer = 0;
		$answer = esc_sql($answer);

		// Convert multiple answers into an array. Text and checkbox answers will arrive as an array.
		if(!is_array($answer)) $answer = array($answer);
		if (!is_array($answer_text)) $answer_text = array($answer_text);
						
		// calculate points
		$points = $_question->calculate_points($question, $answer);
		echo $points."|CHAINEDQUIZ|";
		$total_points = $points + floatval($_POST['points']); // Points for the whole Topic.
		
		// figure out next question
		if ($question->abort_enabled && $total_points >= $question->points_abort_min && $total_points <= $question->points_abort_max) {
			// Abort criteria met. Let's abort the Topic and finish it.
			$next_question = null;
		} else {
			$next_question = $_question->next($question, $answer);
		}

		// Store the answer
		if(!empty($_SESSION['chained_completion_id'])) {
			//$i = 0;
			for ($i = 0; $i < count($answer); $i++) {
			//foreach($answer as $choice_id) {
				$comments = empty($_POST['comments']) ? '' : sanitize_text_field($_POST['comments']);

				// make sure to avoid duplicates and only update the answer if it already exists
				$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".CHAINED_USER_ANSWERS."
					WHERE quiz_id=%d AND completion_id=%d AND question_id=%d AND answer=%d", 
					$quiz->id, intval($_SESSION['chained_completion_id']), $question->id, $answer[$i]));			
				
				if($exists) {
					$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_USER_ANSWERS." SET
						answer=%s, answer_text=%s, points=%f, comments=%s WHERE quiz_id=%d AND completion_id=%d AND question_id=%d", 
						$answer[$i], $answer_text[$i], $points, $comments, $quiz->id, intval($_SESSION['chained_completion_id']), $question->id));			
				}
				else {				
					$wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_USER_ANSWERS." SET
						quiz_id=%d, completion_id=%d, question_id=%d, answer=%s, answer_text=%s, points=%f, comments=%s",
						$quiz->id, intval($_SESSION['chained_completion_id']), $question->id, $answer[$i], $answer_text[$i], $points, $comments));				
				}		
				
				// update the "completed" record as non empty
				$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_COMPLETED." SET not_empty=1 WHERE id=%d", intval($_SESSION['chained_completion_id'])));

				//$i++;
			}
		}
		
		if(!empty($next_question->id)) {
			$question = $next_question;
			$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." 
		 	WHERE quiz_id=%d AND question_id=%d ORDER BY id", $quiz->id, $question->id));
			include(CHAINED_PATH."/views/display-quiz.html.php");
		}
		else {
			// if none, submit the quiz
			 echo $_quiz->finalize($quiz, $total_points); 
		}	 		
	}
}