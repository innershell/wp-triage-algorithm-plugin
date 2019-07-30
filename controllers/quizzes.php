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
		
		
		// Save the quiz.
		if(!empty($_POST['ok']) and check_admin_referer('chained_quiz')) {
			try {
				$_quiz->save($_POST, $_GET['id']);			
				chained_redirect("admin.php?page=chained_quizzes");
			}
			catch(Exception $e) {
				$error = $e->getMessage();
			}
		}
		
		// Select the quiz.
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", intval($_GET['id'])));
		$patient_output = stripslashes($quiz->output);
		$doctor_output = stripslashes($quiz->email_output); 
		
		// Is this quiz currently published?
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
			switch ($_GET['export']) {
				case 'sql':
				self::export_sql(empty($_GET['prefix']) ? "wp_" : $_GET['prefix']);
				exit;
				case 'csv':
				self::export_csv();
				exit;
				default:
				echo 'Something went wrong here';
			}
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
		
		static function export_sql($prefix) {
			global $wpdb;
			$now = gmdate('D, d M Y H:i:s') . ' GMT';	
			$filename = 'orchestra-data-export.sql';	
			header('Content-Type: ' . kiboko_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Pragma: no-cache');
			$newline = kiboko_define_newline();
			
			// Generate SQL for WP_CHAINED_QUIZZES table.
			$quizzes = $wpdb->get_results('SELECT * FROM ' . CHAINED_QUIZZES);
			$i = count($quizzes);
			
			if ($i > 0) {
				echo '--' . $newline;
				echo '-- Generated Time: ' . $now . $newline;
				echo '--' . $newline;
				echo 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";' . $newline;
				echo $newline;
				echo '--' . $newline;
				echo '-- Truncate and insert CHAINED_QUIZZES.' . $newline;
				echo '--' . $newline;
				echo 'TRUNCATE TABLE ' . $prefix . 'chained_quizzes;' .  $newline;
				echo 'INSERT INTO ' . $prefix . 'chained_quizzes (id, title, output, email_admin, email_user, require_login, times_to_take, save_source_url, set_email_output, email_output) VALUES ' . $newline;
				foreach ($quizzes as $quiz) {
					$i--;
					echo "($quiz->id, '" . chained_escape_str($quiz->title) . "', '" . chained_escape_str($quiz->output) . "', $quiz->email_admin, $quiz->email_user, $quiz->require_login, $quiz->times_to_take, $quiz->save_source_url, $quiz->set_email_output, '" . chained_escape_str($quiz->email_output) . "')";
					echo $i > 0 ? ',' : ';';
					echo $newline;
				}
			}
			
			// Generate SQL for CHAINED_QUESTIONS data.
			$questions = $wpdb->get_results('SELECT * FROM ' . CHAINED_QUESTIONS);
			$i = count($questions);
			
			if ($i > 0) {
				echo $newline;
				echo '--' . $newline;
				echo '-- Truncate and insert CHAINED_QUESTIONS.' . $newline;
				echo '--' . $newline;
				echo 'TRUNCATE TABLE ' . $prefix . 'chained_questions;' .  $newline;
				echo 'INSERT INTO ' . $prefix . 'chained_questions (id, quiz_id, title, question, qtype, soap_type, rank, abort_enabled, points_abort_min, points_abort_max, autocontinue, sort_order, accept_comments, accept_comments_label) VALUES ' . $newline;
				foreach ($questions as $question) {
					$i--;
					echo "($question->id, $question->quiz_id, '" . chained_escape_str($question->title) . "', '" . chained_escape_str($question->question) . "', '$question->qtype', '$question->soap_type', $question->rank, $question->abort_enabled, $question->points_abort_min, $question->points_abort_max, $question->autocontinue, $question->sort_order, $question->accept_comments, '$question->accept_comments_label')";
					echo $i > 0 ? ',' : ';';
					echo $newline;
				}
			}
			
			// Generate SQL for CHAINED_CHOICES data.
			$choices = $wpdb->get_results('SELECT * FROM ' . CHAINED_CHOICES);
			$i = count($choices);
			
			if ($i > 0) {
				echo $newline;
				echo '--' . $newline;
				echo '-- Truncate and insert CHAINED_CHOICES.' . $newline;
				echo '--' . $newline;
				echo 'TRUNCATE TABLE ' . $prefix . 'chained_choices;' .  $newline;
				echo 'INSERT INTO ' . $prefix . 'chained_choices (id, quiz_id, question_id, choice, provider_note, assessment, plan, points, is_correct, goto) VALUES ' . $newline;
				foreach ($choices as $choice) {
					$i--;
					echo "($choice->id, $choice->quiz_id, $choice->question_id, '" . chained_escape_str($choice->choice) . "', '" . chained_escape_str($choice->provider_note) . "', '" . chained_escape_str($choice->assessment) . "', '" . chained_escape_str($choice->plan) . "', '$choice->points', '$choice->is_correct', '$choice->goto')";
					echo $i > 0 ? ',' : ';';
					echo $newline;
				}
			}
			
			// Generate SQL for CHAINED_RESULTS data.
			$results = $wpdb->get_results('SELECT * FROM ' . CHAINED_RESULTS);
			$i = count($results);
			
			if ($i > 0) {
				echo $newline;
				echo '--' . $newline;
				echo '-- Truncate and insert CHAINED_RESULTS.' . $newline;
				echo '--' . $newline;
				echo 'TRUNCATE TABLE ' . $prefix . 'chained_results;' .  $newline;
				echo 'INSERT INTO ' . $prefix . 'chained_results (id, quiz_id, points_bottom, points_top, title, description, subjective, objective, assessment, plan, redirect_url) VALUES ' . $newline;
				
				foreach ($results as $result) {
					$i--;
					echo "($result->id, $result->quiz_id, $result->points_bottom, $result->points_top, '" . chained_escape_str($result->title) . "', '" . chained_escape_str($result->description) . "', '" . chained_escape_str($result->subjective) . "', '" . chained_escape_str($result->objective) . "', '" . chained_escape_str($result->assessment) . "', '" . chained_escape_str($result->plan) . "', '$result->redirect_url')";
					echo $i > 0 ? ',' : ';';
					echo $newline;
				}
			}
			
			// Generate SQL for WP_POSTS table.
			$posts = $wpdb->get_results('SELECT * FROM ' . POSTS . ' WHERE post_type != \'revision\'');
			$i = count($posts);
			
			if ($i > 0) {
				echo '--' . $newline;
				echo '-- Generated Time: ' . $now . $newline;
				echo '--' . $newline;
				echo 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";' . $newline;
				echo $newline;
				echo '--' . $newline;
				echo '-- Truncate and insert POSTS.' . $newline;
				echo '-- Note: Post revisions are not dropped.' . $newline;
				echo '--' . $newline;
				echo 'TRUNCATE TABLE ' . $prefix . 'posts;' .  $newline;
				echo 'INSERT INTO ' . $prefix . 'posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) VALUES ' . $newline;
				foreach ($posts as $post) {
					$i--;
					echo "($post->ID, $post->post_author, '$post->post_date', '$post->post_date_gmt', '" . chained_escape_str($post->post_content) . "', '" . chained_escape_str($post->post_title) . "', '" . chained_escape_str($post->post_excerpt) . "', '$post->post_status', '$post->comment_status', '$post->ping_status', '$post->post_password', '$post->post_name', '$post->to_ping', '$post->pinged', '$post->post_modified', '$post->post_modified_gmt', '" . chained_escape_str($post->post_content_filtered) . "', $post->post_parent, '$post->guid', $post->menu_order, '$post->post_type', '$post->post_mime_type', $post->comment_count)";
					echo $i > 0 ? ',' : ';';
					echo $newline;
				}
			}
			
			echo $newline;
			echo '--' . $newline;
			echo '-- End of File' . $newline;
			echo '--' . $newline;
			
			return;
		}
		
		static function export_csv() {
			echo "Feature not available. Coming soon!";
			return;
		}
	}