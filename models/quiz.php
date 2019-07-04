<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizQuiz {
	function add($vars) {
		global $wpdb;
		
		$require_login = empty($vars['require_login']) ? 0 : 1;
		$times_to_take = empty($vars['times_to_take']) ? 0 : intval($vars['times_to_take']);
		$save_source_url = empty($vars['save_source_url']) ? 0 : 1;
		$vars['title'] = sanitize_text_field($vars['title']);
		$email_admin = empty($vars['email_admin']) ? 0 : 1;
      $email_user = empty($vars['email_user']) ? 0 : 1;
      $set_email_output = empty($vars['set_email_output']) ? 0 : 1;
      $email_output = chained_strip_tags($vars['email_output']);
		
		if(!current_user_can('unfiltered_html')) {
			$vars['output'] = strip_tags($vars['output']);
		}
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_QUIZZES." SET
			title=%s, output=%s, email_admin=%d, email_user=%d, require_login=%d, times_to_take=%d, 
			save_source_url=%d, set_email_output=%d, email_output=%s", 
			$vars['title'], $vars['output'], $email_admin, $email_user, $require_login, $times_to_take, 
			$save_source_url, $set_email_output, $email_output));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));

		$quiz_id = $wpdb->insert_id;
		if(!empty($vars['auto_publish'])) $this->auto_publish($quiz_id, $vars);		
		
		return $quiz_id;	
	} // end add
	
	function save($vars, $id) {
		global $wpdb;
		
		$id = intval($id);
		
		$require_login = empty($vars['require_login']) ? 0 : 1;
		$times_to_take = empty($vars['times_to_take']) ? 0 : intval($vars['times_to_take']);
		$save_source_url = empty($vars['save_source_url']) ? 0 : 1;
		$vars['title'] = sanitize_text_field($vars['title']);
		$email_admin = empty($vars['email_admin']) ? 0 : 1;
		$email_user = empty($vars['email_user']) ? 0 : 1;
		$set_email_output = empty($vars['set_email_output']) ? 0 : 1;
		$email_output = chained_strip_tags($vars['email_output']);
		
		if(!current_user_can('unfiltered_html')) {
			$vars['output'] = strip_tags($vars['output']);
		}
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUIZZES." SET
			title=%s, output=%s, email_admin=%d, email_user=%d, require_login=%d, times_to_take=%d, 
			save_source_url=%d, set_email_output=%d, email_output=%s 
			WHERE id=%d", 
			$vars['title'], $vars['output'], $email_admin, $email_user, 
			$require_login, $times_to_take, $save_source_url, $set_email_output, $email_output, $id));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		
		if(!empty($vars['auto_publish'])) $this->auto_publish($id, $vars);
		return true;	
	}
	
	function delete($id) {
		global $wpdb;
		
		$id = intval($id);
		
		// delete questions
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d", $id));
		
		// delete choices
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_CHOICES." WHERE quiz_id=%d", $id));
		
		// delete completed records
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_COMPLETED." WHERE quiz_id=%d", $id));
		
		// delete the quiz
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_QUIZZES." WHERE id=%d", $id));
	}

	function finalize($quiz, $points) {		
	    global $wpdb, $user_ID;
	    
	    $user_id = empty($user_ID) ? 0 : $user_ID;
	    $completion_id = intval(@$_SESSION['chained_completion_id']);
	
		$_result = new ChainedQuizResult();
		// calculate result
		$result = $_result->calculate($quiz, $points);
		
		// get final screen and replace vars
		$snapshot = ''; // The SOAP note data to be saved in the submission record snapshot.
		$output = stripslashes($quiz->output);
		$email_output = $quiz->set_email_output ? stripslashes($quiz->email_output) : $output;
		
		$output = str_replace('{{result-title}}', @$result->title, $output);
		$output = str_replace('{{result-text}}', stripslashes(@$result->description), $output);
		$output = str_replace('{{points}}', $points, $output);
		$output = str_replace('{{questions}}', $_POST['total_questions'], $output);
		
		if(strstr($output, '{{answers-table}}')) {
			$snapshot = $this->answers_table($completion_id);
			$output = str_replace('{{answers-table}}', $snapshot, $output);
		}

		if ($snapshot == '') {
			$snapshot = $this->soap_note($completion_id);
		}

		if(strstr($output, '{{soap-note}}')) {
			$output = str_replace('{{soap-note}}', $snapshot, $output);
		}
		
		$email_output = str_replace('{{result-title}}', @$result->title, $email_output);
		$email_output = str_replace('{{result-text}}', stripslashes(@$result->description), $email_output);
		$email_output = str_replace('{{points}}', $points, $email_output);
		$email_output = str_replace('{{questions}}', $_POST['total_questions'], $email_output);
		
		if(strstr($email_output, '{{answers-table}}')) {
			$email_output = str_replace('{{answers-table}}', $this->answers_table($completion_id), $email_output);
		}
		
		if(strstr($email_output, '{{soap-note}}')) {
			$email_output = str_replace('{{soap-note}}', $this->soap_note($completion_id), $email_output);
		}
		
		// Email attachment
		/** TODO: Fetch the email method from config. */
		$email_content_method = 'inline';
		$email_content_method = 'attach';

		if ($email_content_method == 'attach') {
			// Write to file.
			$file = plugin_dir_path( __DIR__ ) . '/output_files/'.$completion_id.'.html'; 
			$open = fopen( $file, "a" ); // Open the file for writing (a) only.
			$write = fputs( $open, $email_output ); 
			fclose( $open );

			// Send email with result in attachment.
			$this->send_emails($quiz, "SOAP note for diabetes triage.", $file); // Sends email to either the performing USER or ADMIN.
		} else {
			$this->send_emails($quiz, $email_output, null); // Sends email to either the performing USER or ADMIN.
		}
		
		
		$GLOBALS['chained_completion_id'] = $completion_id;
		$GLOBALS['chained_result_id'] = @$result->id;
		$output = do_shortcode($output);
		$output = wpautop($output);
		
		// only if the quiz is published on more than one page, store info about source url
		$source_url = '';
		if(!empty($quiz->save_source_url)) $source_url = esc_url_raw($_SERVER['HTTP_REFERER']);	
		
		$user_email = '';
		if($user_ID) {
			$user = get_userdata($user_ID);
			$user_email = $user->user_email;
		}
			
		if(!empty($_POST['chained_email'])) {
			$user_email = sanitize_email($_POST['chained_email']);
		}	 
		
		// now insert in completed
		if(!empty($_SESSION['chained_completion_id'])) {
			$wpdb->query( $wpdb->prepare("UPDATE ".CHAINED_COMPLETED." SET
				quiz_id = %d, points = %f, result_id = %d, datetime = NOW(), ip = %s, user_id = %d, 
				snapshot = %s, source_url=%s, email=%s WHERE id=%d",
				$quiz->id, $points, @$result->id, $_SERVER['REMOTE_ADDR'], $user_id, $snapshot, 
				$source_url, $user_email, intval($_SESSION['chained_completion_id'])));
			$taking_id = $_SESSION['chained_completion_id'];	
			unset($_SESSION['chained_completion_id']);	
		}		
		// normally this shouldn't happen, but just in case
		else {			
			$wpdb->query( $wpdb->prepare("INSERT INTO ".CHAINED_COMPLETED." SET
				quiz_id = %d, points = %f, result_id = %d, datetime = NOW(), ip = %s, user_id = %d, snapshot = %s, 
				source_url=%s, email=%s",
				$quiz->id, $points, @$result->id, $_SERVER['REMOTE_ADDR'], $user_id, $snapshot, $source_url, $user_email));		 	
			$taking_id = $wpdb->insert_id;		
		}
		
		// send API call for other plugins
		do_action('chained_quiz_completed', $taking_id);
		
		// if the result needs to redirect, replace the output with the redirect URL
		if(!empty($result->redirect_url)) $output = "[CHAINED_REDIRECT]".$result->redirect_url;
		
		return $output;
   	} // end finalize
   
   	// send email to user and admin if required
   	function send_emails($quiz, $output, $attach_path) {
		global $user_ID;
		$attachments = array ($attach_path);
		
		if(empty($quiz->email_admin) and empty($quiz->email_user)) return true;
		$admin_email = chained_admin_email();
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.$admin_email . "\r\n";
			
			$admin_output = $user_output = $output;
			
			if(strstr($output, '{{{split}}}')) {
				$parts = explode('{{{split}}}', $output);
				$user_output = trim($parts[0]);
				$admin_output = trim($parts[1]);
			}
		
		if(!empty($quiz->email_admin)) {   		
			$subject = stripslashes(get_option('chained_admin_subject'));
			$subject = str_replace('{{quiz-name}}', stripslashes($quiz->title), $subject);
			
				if($user_ID) {
					$user = get_userdata($user_ID);
					$user_msg = sprintf(__('Username: %s <br> User email: %s', 'chained'), $user->user_login, $user->user_email); 
				}   		
				else {
					$user_msg = sprintf(__('User IP: %s', 'chained'), $_SERVER['REMOTE_ADDR']);
					if(!empty($_POST['chained_email'])) $user_msg .= "<br>". sprintf(__('User email: %s', 'chained'), $_POST['chained_email']);
				}
			
			$message='<html><head><title>'.$subject.'</title>
				</head>
				<html><body>'.wpautop($admin_output).'</body></html>';	
						
				wp_mail($admin_email, $subject, $message, $headers, $attachments);
		}
		
		if(!empty($quiz->email_user)) {
			$subject = stripslashes(get_option('chained_user_subject'));
			$subject = str_replace('{{quiz-name}}', stripslashes($quiz->title), $subject);
			
				if($user_ID) {
					$user = get_userdata($user_ID);
					$user_email = $user->user_email;
				}   		
				else {
					$user_email = $_POST['chained_email'];
				}
				
				if(empty($user_email)) return false;
			
			$message='<html><head><title>'.$subject.'</title>
				</head>
				<html><body>'.wpautop($user_output).'</body></html>';
				
				wp_mail($user_email, $subject, $message, $headers, $attachments);
		}
	} // end send_emails()
	
	function auto_publish($quiz_id, $vars) {
		global $wpdb;
	
		$post = array('post_content' => '[chained-quiz '.$quiz_id.']', 'post_name'=> sanitize_text_field($vars['title']), 
			'post_title'=>sanitize_text_field($vars['title']), 'post_status'=>'publish');
		wp_insert_post($post);
	}
	
	// creates a table from questions and answers along with correct / wrong answer and points collected
	function answers_table($completion_id) {
		global $wpdb;
		
		$_question = new ChainedQuizQuestion();
		
		$answers = $wpdb->get_results($wpdb->prepare("SELECT tUA.*, 
			tC.choice as choice, tC.is_correct as choice_correct,
			tC.assessment as assessment, tC.plan as plan,
			tQ.question as question, tQ.qtype as qtype, tQ.soap_type as soap_type
			FROM ".CHAINED_USER_ANSWERS." tUA
			JOIN ".CHAINED_QUESTIONS." tQ ON tQ.id = tUA.question_id
			LEFT JOIN ".CHAINED_CHOICES." tC ON tC.id = tUA.answer
			WHERE tUA.completion_id=%d ORDER BY tUA.ID", $completion_id));
			
		/*$output = '<table class="chained-quiz-answers"><tr><th>'.__('Question', 'chained').'</th>
		<th>'.__('Answer', 'chained').'</th><th>'.__('Correct answer?', 'chained').'</th>
		<th>'.__('Points', 'chained').'</th></tr>';*/

		$output = '<table class="chained-quiz-answers"><tr>
			<th>'.__('Question', 'chained').'</th>
			<th>'.__('Patient Answer', 'chained').'</th>
			<th>'.__('Soap Type','chained').'</th>
			<th>'.__('Assessment','chained').'</th>
			<th>'.__('Plan', 'chained').'</th></tr>';
		
		foreach($answers as $answer) {
			// prepare answer and correct info for checkboxes and other answers
			$user_answer = $is_correct = '';
			if($answer->qtype == 'text') {
				$user_answer = wpautop(stripslashes($answer->answer_text));
				$is_correct = $wpdb->get_var($wpdb->prepare("SELECT is_correct FROM ".CHAINED_CHOICES."
					WHERE question_id=%d AND choice=%s", $answer->question_id, $answer->answer));
				$is_correct = $is_correct ? __('Yes', 'chained') : __('No', 'chained');	
			}
			else {
				// Answer is a radio button or single checkbox answer.
				if(is_numeric($answer->answer)) {
					$user_answer = wpautop(stripslashes($answer->choice));
					$is_correct = $answer->choice_correct ? __('Yes', 'chained') : __('No', 'chained');	
				}
				// Answer is a multiple checkbox answer.
				else {
					$choice_ids = explode(',', $answer->answer);
					foreach($choice_ids as $cnt=>$choice_id) {
						$choice_text = $wpdb->get_row($wpdb->prepare("SELECT choice, is_correct FROM ".CHAINED_CHOICES." WHERE id=%d", $choice_id));
						if($cnt) { 
							$user_answer .= '<br><br> ';
							$is_correct .= ', ';
						} 
						$user_answer .= stripslashes($choice_text->choice);
						$is_correct .= $choice_text->is_correct ? __('Yes', 'chained') : __('No', 'chained');	
					}
					$user_answer = wpautop($user_answer);
				}
			}
			
			$output .= '<tr>';
			
			// The question.
			$output .= '<td>'.$_question->display_question($answer).'</td>';
			
			// The patient's answer.
			$output .= '<td>'.$user_answer.'</td>';
			
			// The question's SOAP type.
			$output .= '<td>';
				if ($answer->soap_type == 's') {
					$output .= 'Subjective';
				} else if ($answer->soap_type == 'o') {
					$output .= 'Objective';
				} else if ($answer->soap_type == 'a') {
					$output .= 'Assessment';
				} else if ($answer->soap_type == 'p') {
					$output .= 'Plan';
				} else {
					$output .= 'None';
				}
			$output .= '</td>';			

			// The assessment and plan.
			$output .= '<td>'.$answer->assessment.'</td>';
			$output .= '<td>'.$answer->plan.'</td>';

			$output .= '</tr>';
		}
		
		$output .= '</table>';
		
		return $output;	
	} // end answers_table

	/**
	 * FUNCTION: SOAP_NOTE builds a standardized medical note using the user's answers and question config.
	 */
	function soap_note($completion_id) {
		global $wpdb;		
		$_question = new ChainedQuizQuestion();
		$output = '';

		$answers = $wpdb->get_results($wpdb->prepare("SELECT tUA.*, tC.choice as choice, tC.is_correct as choice_correct,
		tC.provider_note as provider_note, tC.assessment as assessment, tC.plan as plan,
		tQ.question as question, tQ.qtype as qtype, tQ.soap_type as soap_type
		FROM ".CHAINED_USER_ANSWERS." tUA
		JOIN ".CHAINED_QUESTIONS." tQ ON tQ.id = tUA.question_id
		LEFT JOIN ".CHAINED_CHOICES." tC ON tC.id = tUA.answer
		WHERE tUA.completion_id=%d ORDER BY tUA.ID", $completion_id));
		
		// Setup the basic table header.
		$output .= '<table border="1" cellspacing="0" cellpadding="10">';

		// NONE
		$count = 0; // To count how many answers in this category so that 'None (N/A)' can be inserted, as necessary.
		$output .= '<tr><th colspan="2">Orchestra Response</th></tr>
					<tr><td colspan="2"><ul>';
		
		for ($i = 0; $i < count($answers); $i++) {
			$user_answer = '';

			// Display the provider note.
			if ($answers[$i]->soap_type == 'n') {
				if (!empty($answers[$i]->answer)) {
					$user_answer .= '<li>';

					// For text questions, the note is a prepared answer using text substitution from the user's input.
					// For non-text questions, the note uses the provider note value setup by admin in the config.
					//    ** If the admin didn't setup a provider note, then it just shows the user's raw answers.
					if ($answers[$i]->qtype == 'text') {
						$current_question_id = $answers[$i]->question_id;
						while($answers[$i]->question_id == $current_question_id) {
							if (strlen($user_answer) > 0) $user_answer .= ' ';
							$user_answer .= stripslashes($answers[$i]->provider_note) . '<strong> ' . stripslashes($answers[$i]->answer_text) . '</strong>';
							$i++;
						}
						$user_answer .= ".";
						$i--;
					} 
					// Warning that no a provider note was not setup by the admin for this radio/checkbox answer.
					elseif (empty($answers[$i]->provider_note)) {
						$user_answer .= 'ALERT: PROVIDER_NOTE not setup for the following question/answer: ' . stripslashes($answers[$i]->question) . '/' . stripslashes($answers[$i]->choice);
					} 
					// The user's answer is displayed using the provider note for the answer.
					else {
						$user_answer .= $answers[$i]->provider_note;
					}
					
					$user_answer .= '</li>';
					$count++;
				}
			}
			$output .= $user_answer;
		}
		$output .= $count == 0 ? 'None (N/A)' : '';
		$output .= '</ul>';

		// SUBJECTIVE
		$count = 0; // To count how many answers in this category so that 'None (N/A)' can be inserted, as necessary.
		$output .= '<tr><th colspan="2">S (Subjective)</th></tr>
					<tr><td colspan="2"><ul>';
		
		for ($i = 0; $i < count($answers); $i++) {
			$user_answer = '';

			// Display the provider note.
			if ($answers[$i]->soap_type == 's') {
				if (!empty($answers[$i]->answer)) {
					$user_answer .= '<li>';

					// For text questions, the note is a prepared answer using text substitution from the user's input.
					// For non-text questions, the note uses the provider note value setup by admin in the config.
					//    ** If the admin didn't setup a provider note, then it just shows the user's raw answers.
					if ($answers[$i]->qtype == 'text') {
						$current_question_id = $answers[$i]->question_id;
						while($answers[$i]->question_id == $current_question_id) {
							if (strlen($user_answer) > 0) $user_answer .= ' ';
							$user_answer .= stripslashes($answers[$i]->provider_note) . '<strong> ' . stripslashes($answers[$i]->answer_text) . '</strong>';
							$i++;
						}
						$user_answer .= ".";
						$i--;
					} 
					// Warning that no a provider note was not setup by the admin for this radio/checkbox answer.
					elseif (empty($answers[$i]->provider_note)) {
						$user_answer .= 'ALERT: PROVIDER_NOTE not setup for the following question/answer: ' . stripslashes($answers[$i]->question) . '/' . stripslashes($answers[$i]->choice);
					} 
					// The user's answer is displayed using the provider note for the answer.
					else {
						$user_answer .= $answers[$i]->provider_note;
					}
					
					$user_answer .= '</li>';
					$count++;
				}
			} 
			$output .= $user_answer;
		}
		$output .= $count == 0 ? 'None (N/A)' : '';
		$output .= '</ul></td></tr>';

		// OBJECTIVE
		$count = 0; // To count how many answers in this category so that 'None (N/A)' can be inserted, as necessary.
		$output .= '<tr><th colspan="2">O (Objective)</th></tr>
			<tr><td colspan="2"><ul>';
		
			for ($i = 0; $i < count($answers); $i++) {
				$user_answer = '';
	
				// Display the provider note.
				if ($answers[$i]->soap_type == 'o') {
					if (!empty($answers[$i]->answer)) {
						$user_answer .= '<li>';
	
						// For text questions, the note is a prepared answer using text substitution from the user's input.
						// For non-text questions, the note uses the provider note value setup by admin in the config.
						//    ** If the admin didn't setup a provider note, then it just shows the user's raw answers.
						if ($answers[$i]->qtype == 'text') {
							$current_question_id = $answers[$i]->question_id;
							while($answers[$i]->question_id == $current_question_id) {
								if (strlen($user_answer) > 0) $user_answer .= ' ';
								$user_answer .= stripslashes($answers[$i]->provider_note) . ' <strong>' . stripslashes($answers[$i]->answer_text) . '</strong>';
								$i++;
							}
							$user_answer .= ".";
							$i--;
						} 
						// Warning that no a provider note was not setup by the admin for this radio/checkbox answer.
						elseif (empty($answers[$i]->provider_note)) {
							$user_answer .= 'ALERT: PROVIDER_NOTE not setup for the following answer: ' . stripslashes($answers[$i]->choice);
						} 
						// The user's answer is displayed using the provider note for the answer.
						else {
							$user_answer .= $answers[$i]->provider_note;
						}
						
						$user_answer .= '</li>';
						$count++;
					}
				} 
				$output .= $user_answer;
			}		
			$output .= $count == 0 ? 'None (N/A)' : '';
			$output .= '</ul></td></tr>';


		// ASSESSMENT and PLAN Side-by-Side
		$count = 0; // To count how many answers in this category so that 'None (N/A)' can be inserted, as necessary.
		$output .= '<tr><th>A (Assessment)</th><th>P (Plan)</th></tr>';
		$user_answer = '';
		foreach ($answers as $answer) {
			if (!empty($answer->assessment) && !empty($answer->plan)) {
				$user_answer .= '<tr><td width="50%">'.$answer->assessment.'</td><td width="50%">'.$answer->plan.'</td></tr>';
				$count++;
			}
		}

		$output .= $count == 0 ? '<tr><td width="50%">None (N/A)</td><td width="50%">None (N/A)</td></tr>' : $user_answer;
		$output .= '</table>';
		
		return $output;	
	} // end soap-note


	/**
	 * FUNCTION: SOAP_NOTE builds a standardized medical note using the user's answers and question config.
	 */
	function soap_note_fax($completion_id) {
		global $wpdb;		
		$_question = new ChainedQuizQuestion();
		$output = '';

		$answers = $wpdb->get_results($wpdb->prepare("SELECT tUA.*, tC.choice as choice, tC.is_correct as choice_correct,
		tC.provider_note as provider_note, tC.assessment as assessment, tC.plan as plan,
		tQ.question as question, tQ.qtype as qtype, tQ.soap_type as soap_type
		FROM ".CHAINED_USER_ANSWERS." tUA
		JOIN ".CHAINED_QUESTIONS." tQ ON tQ.id = tUA.question_id
		LEFT JOIN ".CHAINED_CHOICES." tC ON tC.id = tUA.answer
		WHERE tUA.completion_id=%d ORDER BY tUA.ID", $completion_id));
		
		// SUBJECTIVE
		$output .= 'S (SUBJECTIVE)<BR>';
		
		for ($i = 0; $i < count($answers); $i++) {
			$user_answer = '';

			// Display the provider note.
			if ($answers[$i]->soap_type == 's') {
				if (!empty($answers[$i]->answer)) {
					$user_answer .= '(';

					// For text questions, the note is a prepared answer using text substitution from the user's input.
					// For non-text questions, the note uses the provider note value setup by admin in the config.
					//    ** If the admin didn't setup a provider note, then it just shows the user's raw answers.
					if ($answers[$i]->qtype == 'text') {
						$current_question_id = $answers[$i]->question_id;
						while($answers[$i]->question_id == $current_question_id) {
							if (strlen($user_answer) > 0) $user_answer .= ' ';
							$user_answer .= stripslashes($answers[$i]->provider_note) . ' <strong>' . stripslashes($answers[$i]->answer_text) . '</strong>';
							$i++;
						}
						$user_answer .= ".";
						$i--;
					} 
					// Warning that no a provider note was not setup by the admin for this radio/checkbox answer.
					elseif (empty($answers[$i]->provider_note)) {
						$user_answer .= 'ALERT: PROVIDER_NOTE not setup for the following question/answer: ' . stripslashes($answers[$i]->question) . '/' . stripslashes($answers[$i]->choice);
					} 
					// The user's answer is displayed using the provider note for the answer.
					else {
						$user_answer .= $answers[$i]->provider_note;
					}
					$user_answer .= ') ';
				}
			}
			$output .= $user_answer;
		}
		$output .= '<BR><BR>';

		// OBJECTIVE
		$output .= 'O (OBJECTIVE)<BR>';
		
			for ($i = 0; $i < count($answers); $i++) {
				$user_answer = '';
	
				// Display the provider note.
				if ($answers[$i]->soap_type == 'o') {
					if (!empty($answers[$i]->answer)) {
						$user_answer .= '(';
	
						// For text questions, the note is a prepared answer using text substitution from the user's input.
						// For non-text questions, the note uses the provider note value setup by admin in the config.
						//    ** If the admin didn't setup a provider note, then it just shows the user's raw answers.
						if ($answers[$i]->qtype == 'text') {
							$current_question_id = $answers[$i]->question_id;
							while($answers[$i]->question_id == $current_question_id) {
								if (strlen($user_answer) > 0) $user_answer .= ' ';
								$user_answer .= stripslashes($answers[$i]->provider_note) . ' <strong>' . stripslashes($answers[$i]->answer_text) . '</strong>';
								$i++;
							}
							$user_answer .= ".";
							$i--;
						} 
						// Warning that no a provider note was not setup by the admin for this radio/checkbox answer.
						elseif (empty($answers[$i]->provider_note)) {
							$user_answer .= 'ALERT: PROVIDER_NOTE not setup for the following answer: ' . stripslashes($answers[$i]->choice);
						} 
						// The user's answer is displayed using the provider note for the answer.
						else {
							$user_answer .= $answers[$i]->provider_note;
						}
						$user_answer .= ') ';
					}
				}
				$output .= $user_answer;
			}
		$output .= '<BR><BR>';

		// ASSESSMENT
		$output .= 'A (ASSESSMENT)<BR>';
		
		foreach ($answers as $answer) {
			if (!empty($answer->assessment)) {
				$output .= '(' . $answer->assessment . ') ';
			}			
		}
		$output .= '<BR><BR>';

		// PLAN
		$output .= 'P (PLAN)<BR>';
		
		foreach ($answers as $answer) {
			if (!empty($answer->plan)) {
				$output .= '(' . $answer->plan . ') ';	
			}
		}
		
		return $output;	
	} // end soap_note_fax
	
	// copy / duplicate quiz
	static function copy($id) {
	   global $wpdb;
      $id = intval($id);
	   
	   // select & copy quiz
      $quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $id), ARRAY_A);
      $quiz['title'] = sprintf(__('%s (Copy)', 'chained'), stripslashes($quiz['title']));
      $quiz['output'] = stripslashes($quiz['output']);
      $_quiz = new ChainedQuizQuiz();  
      $new_id = $_quiz->add($quiz);
	   
	   // select & copy results
	   $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." WHERE quiz_id=%d ORDER BY id", $id), ARRAY_A);
	   $_result = new ChainedQuizResult();
	   foreach($results as $result) {
	      $result['quiz_id'] = $new_id;
	      $result['title'] = stripslashes($result['title']);
         $result['description'] = stripslashes($result['description']);
         $_result->add($result);
	   }
	   
	   // select & copy questions and choices
	   $id_matches = array(); // we'll use this to match old IDs with new IDs. Important because choices may contain ID of new question in "goto"
	   $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . CHAINED_QUESTIONS . " WHERE quiz_id=%d ORDER BY sort_order", $id), ARRAY_A);
	   $_question = new ChainedQuizQuestion();
	   foreach($questions as $cnt => $question) {
	      $question['title'] = stripslashes($question['title']);
	      $question['question'] = stripslashes($question['question']);
	      $question['quiz_id'] = $new_id;
	      $new_q_id = $_question->add($question);
	      $id_matches[$question['id']] = $new_q_id;
	      $questions[$cnt]['new_id'] = $new_q_id;
	   }
	   
	   // now transfer choices
	   foreach($questions as $question) {
	      $choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ". CHAINED_CHOICES." WHERE question_id=%d ORDER BY id", $question['id']));
	      foreach($choices as $choice) {
	         $choice->choice = stripslashes($choice->choice);
	         $choice->question_id = $question['new_id'];
	         if(is_numeric($choice->goto)) $choice->goto = @$id_matches[$choice->goto];
	         
	         $wpdb->query($wpdb->prepare("INSERT INTO " . CHAINED_CHOICES . " SET quiz_id=%d, question_id=%d, choice=%s, provider_note=%s, assessment=%s, plan=%s, points=%f, is_correct=%d, goto=%s",
   	         $new_id, $choice->question_id, $choice->choice, $choice->provider_note, $choice->assessment, $choice->plan, $choice->points, $choice->is_correct, $choice->goto));
	      } // end foreach choice
	   }
	} // end copy()
}