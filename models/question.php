<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizQuestion {


	/**************************************************************************
	 * FUNCTION: Add a new question.
	 **************************************************************************/
	function add($vars) {
		global $wpdb;
		
		$vars['title'] = sanitize_text_field($vars['title']);		
		if(!in_array($vars['qtype'], array('radio', 'checkbox', 'field', 'text'))) $vars['qtype'] = 'none';
		if(!current_user_can('unfiltered_html')) {
			$vars['question'] = strip_tags($vars['question']);
		}
		$accept_comments = empty($vars['accept_comments']) ? 0 : 1;
		$accept_comments_label = sanitize_text_field($vars['accept_comments_label']);
		
		// sort order
		$sort_order = $wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".CHAINED_QUESTIONS."
			WHERE quiz_id=%d", $vars['quiz_id']));
		$sort_order++;	 
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".CHAINED_QUESTIONS." SET
			quiz_id=%d, question=%s, qtype=%s, soap_type=%s, rank=%d, 
			abort_enabled=%d, points_abort_min=%f, points_abort_max=%f, 
			title=%s, autocontinue=%d, sort_order=%d, accept_comments=%d, accept_comments_label=%s", 
			intval($vars['quiz_id']), $vars['question'], $vars['qtype'], $vars['soap_type'], intval(@$vars['rank']), 
			$vars['abort_enabled'], $vars['points_abort_min'], $vars['points_abort_max'], 
			$vars['title'], intval(@$vars['autocontinue']), $sort_order, $accept_comments, $accept_comments_label));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return $wpdb->insert_id;	
	}


	/**************************************************************************
	 * FUNCTION: Save changes to a question.
	 **************************************************************************/
	function save($vars, $id) {
		global $wpdb;
		
		$vars['title'] = sanitize_text_field($vars['title']);
		if(!in_array($vars['qtype'], array('radio', 'checkbox', 'field', 'text', 'date'))) $vars['qtype'] = 'none';
		if(!current_user_can('unfiltered_html')) {
			$vars['question'] = strip_tags($vars['question']);
		}
		
		$abort_enabled = empty($vars['abort_enabled']) ? 0 : 1;
		$accept_comments = empty($vars['accept_comments']) ? 0 : 1;
		$accept_comments_label = sanitize_text_field($vars['accept_comments_label']);
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUESTIONS." SET
			question=%s, qtype=%s, soap_type=%s, title=%s, 
			abort_enabled=%d, points_abort_min=%f, points_abort_max=%f, 
			autocontinue=%d, accept_comments=%d, accept_comments_label=%s WHERE id=%d", 
			$vars['question'], $vars['qtype'], $vars['soap_type'], $vars['title'], 
			$abort_enabled, $vars['points_abort_min'], $vars['points_abort_max'], 
			intval(@$vars['autocontinue']), $accept_comments, $accept_comments_label, $id));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	

	/**************************************************************************
	 * FUNCTION: Deletes a question by ID.
	 **************************************************************************/
	function delete($id) {
		global $wpdb;
	
		// delete choices		
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_CHOICES." WHERE question_id=%d", $id));
		
		// delete question
		$result = $wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_QUESTIONS." WHERE id=%d", $id));
		
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	

	/**************************************************************************
	 * FUNCTION: Saves the choices on a question.
	 **************************************************************************/	
	function save_choices($vars, $id) {
		global $wpdb;
		
		// edit/delete existing choices
		$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." WHERE question_id=%d ORDER BY id ", $id));
		
		foreach($choices as $choice) {
			if(!empty($_POST['dels']) and in_array($choice->id, $_POST['dels'])) {
				$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_CHOICES." WHERE id=%d", $choice->id));
				continue;
			}
			
			if(!current_user_can('unfiltered_html')) {
				$_POST['answer'.$choice->id] = strip_tags($_POST['answer'.$choice->id]);
				$_POST['patient_note'.$choice->id] = strip_tags($_POST['patient_note'.$choice->id]);
				$_POST['provider_note'.$choice->id] = strip_tags($_POST['provider_note'.$choice->id]);
				$_POST['assessment'.$choice->id] = strip_tags($_POST['assessment'.$choice->id]);
				$_POST['plan'.$choice->id] = strip_tags($_POST['plan'.$choice->id]);
			}

			$_POST['goto'.$choice->id] = sanitize_text_field($_POST['goto'.$choice->id]);
			if(!is_numeric($_POST['points'.$choice->id])) $_POST['points'.$choice->id] = 0;
			
			// else update
			$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_CHOICES." SET
				choice=%s, points=%s, patient_note=%s, provider_note=%s, 
				assessment=%s, plan=%s, is_correct=%d, goto=%s WHERE id=%d", 
				$_POST['answer'.$choice->id], $_POST['points'.$choice->id], $_POST['patient_note'.$choice->id], $_POST['provider_note'.$choice->id], 
				$_POST['assessment'.$choice->id], $_POST['plan'.$choice->id], intval(@$_POST['is_correct'.$choice->id]), $_POST['goto'.$choice->id], $choice->id));
		}	
		
		// add new choices
		$i = 0;
		$counter = 1;
		$correct_array = @$_POST['is_correct'];
		$provider_note = $_POST['provider_notes'];
		$assessments = $_POST['assessments'];
		$plans = $_POST['plans'];
		foreach($_POST['answers'] as $answer) {
			$correct = @in_array($counter, $correct_array) ? 1 : 0;
			$counter++;
			if($answer === '') continue;
			
			if(!current_user_can('unfiltered_html')) {
				$answer = strip_tags($answer);
				$assessments[$i] = strip_tags($assessments[$i]);
				$plans[$i] = strip_tags($plans[$i]);
			}

			if(!is_numeric($_POST['points'][($counter-2)])) $_POST['points'][($counter-2)] = 0;
		
			// now insert the choice
			$wpdb->query( $wpdb->prepare("INSERT INTO ".CHAINED_CHOICES." SET
				question_id=%d, choice=%s, provider_note=%s, assessment=%s, plan=%s, points=%s, is_correct=%d, goto=%s, quiz_id=%d", 
				$id, $answer, $provider_note[$i], $assessments[$i], $plans[$i], $_POST['points'][($counter-2)], $correct, $_POST['goto'][($counter-2)], 
				intval($_POST['quiz_id'])) );
			
			$i++;
		}
	}


	/**************************************************************************
	 * FUNCTION: Displays the question contents.
	 **************************************************************************/
	function display_question($question) {
		// for now only add stripslashes and autop, we'll soon add a filter like in Watupro
		$content = stripslashes($question->question);
		//$content = wpautop($content);
		$content = do_shortcode($content);
		return $content;
	}


	/**************************************************************************
	 * FUNCTION: Display all the possible choices on a question.
	 **************************************************************************/	
	function display_choices($question, $choices) {
		$autocontinue = $output = '';
		if($question->qtype == 'radio' and $question->autocontinue) {
			$autocontinue = "onclick=\"chainedQuiz.goon(".$question->quiz_id.", '".admin_url('admin-ajax.php')."');\"";
			$output .= '<!--hide_go_ahead-->';
		}  	   
		
			switch($question->qtype) {
				case 'text':
					$type = $question->qtype;
					foreach($choices as $choice) {
						$output .= "
							<div class='chained-quiz-choice'>
								<input type='hidden' name='answers[]' value='".$choice->id."'>
								<textarea class='chained-quiz-frontend chained-quiz-$type' required='required' name='answer_texts[]' placeholder='".$choice->choice."' rows='3' cols='40'></textarea>
							</div>";
					}					
					return $output;
					break;
				case 'date':
					$type = $question->qtype;
					foreach($choices as $choice) {
						$output .= "
							<div class='chained-quiz-choice'>
								<input type='hidden' name='answers[]' value='".$choice->id."'>
								<input class='chained-quiz-frontend chained-quiz-$type' type='$type' name='answer_texts[]' placeholder='".$choice->choice."'>
							</div>";
					}					
					return $output;
					break;
				case 'radio':
					$type = $question->qtype;
					$name = "answer";
					
					foreach($choices as $choice) {
						$choice_text = stripslashes($choice->choice);
						$choice_text = do_shortcode($choice_text);						
						$output .= "<div class='chained-quiz-choice'><label class='chained-quiz-label'><input class='chained-quiz-frontend chained-quiz-$type' type='$type' name='$name' value='".$choice->id."' $autocontinue> $choice_text</label></div>";
					}

					return $output;
					break;
				case 'checkbox':
					$type = $question->qtype;
					$name = "answers[]";
					$noneID = null;

					// Find the 'None' answer choice and cleanup data at the same time while we are already inspecting each element.
					for ($i=0; $i<count($choices); $i++) {
						$choices[$i]->choice = do_shortcode(stripslashes($choices[$i]->choice));
						if ($choices[$i]->choice == 'None') $noneID = $choices[$i]->id;
					}
					
					foreach($choices as $choice) {
						$output .= "<div class='chained-quiz-choice'><label class='chained-quiz-label'>";						

						if ($choice->id == $noneID) {
							$output .= "<input class='chained-quiz-frontend chained-quiz-$type' type='$type' name='$name' value='".$choice->id."' onclick='deselectAllCheckboxes(".$noneID.");' $autocontinue> $choice->choice</label></div>";
						} else {
							$output .= "<input class='chained-quiz-frontend chained-quiz-$type' type='$type' name='$name' value='".$choice->id."' onclick='deselectNoneCheckbox(".$noneID.");' $autocontinue> $choice->choice</label></div>";
						}
					}
					return $output;
					break;
			}
	}
  

	/**************************************************************************
	 * FUNCTION: Calculate the points of a given answer.
	 **************************************************************************/
	function calculate_points($question, $answer) {
		global $wpdb;
		
		$ids = array(0);
		if(is_array($answer) and !empty($answer[0])) {
			$answer = chained_int_array($answer);
			$ids = array_merge($ids, $answer);
		}
		else $ids[] = intval($answer);
		
		// select points
		if($question->qtype != 'text') {
			$points = $wpdb->get_var($wpdb->prepare("SELECT SUM(points) FROM ".CHAINED_CHOICES."
				WHERE question_id=%d AND id IN (".implode(",", $ids).")", $question->id));
		}
		else {
			$points = $wpdb->get_var($wpdb->prepare("SELECT points FROM ".CHAINED_CHOICES."
				WHERE question_id=%d AND choice LIKE %s", $question->id, $answer));
			}
		return $points;	
	}


	/**************************************************************************
	 * FUNCTION: Get the next configured question in the Topic.
	 * 
	 * NOTES:
	 * $question = SELECT * FROM CHAINED_QUESTIONS WHERE id=%d
	 * $answer = The CHOICE_ID of text/radio or array of CHOICE_IDs if checkbox.
	 **************************************************************************/	
	function next($question, $answer) {
		global $wpdb;
		$goto = array();
		$answer_ids = array(0);
		
		// For questions with multiple answers, build an array of answer IDs.
		if(is_array($answer)) {
			foreach($answer as $ans) {
				if(!empty($ans)) $answer_ids[] = $ans;
			}
		} 
		else {
			if(!empty($answer)) $answer_ids[] = $answer;
		} 
				
		/** Build a questions queue to follow-up until all questions have been asked.
		 *  1. Find follow-up questions and sort by order to be asked.
		 *  2. Ask the first follow-up question in the list.
		 *  3. Store the remaining questions in a queue to be asked later.
		 * */

		/** 
		 * 1. First query is for the goto questions that have a numeric ID.
		 * 2. Second query is for the goto questions that have a 'next' ID.
		 * 3. Third query is for the goto questions that have a 'finalize' ID.		
		 * */
		$choices = $wpdb->get_results($wpdb->prepare(
			"SELECT id AS goto, sort_order 
			FROM ".CHAINED_QUESTIONS." WHERE id IN (
				SELECT goto FROM ".CHAINED_CHOICES." 
				WHERE question_id=%d 
				AND id IN (".implode(",", $answer_ids).")
			)
			UNION
			SELECT 'next', 'yyy' AS sort_order
			FROM ".CHAINED_CHOICES." 
			WHERE question_id=%d 
			AND goto='next' 
			AND id IN (".implode(",", $answer_ids).")
			UNION
			SELECT 'finalize', 'zzz' AS sort_order 
			FROM ".CHAINED_CHOICES." 
			WHERE question_id=%d 
			AND goto='finalize'
			AND id IN (".implode(",", $answer_ids).")"
			, $question->id, $question->id, $question->id));

		// Restore the goto queue from outstanding goto questions.
		$goto = $_SESSION['chained_goto_queue'];

		// Add new new goto questions to the queue.
		foreach($choices as $choice) {
			$goto[$choice->goto] = $choice->sort_order;
		}

		// Now sort queue to figure out what's the next question and put the remaining questions into the session queue.
		asort($goto);											// Sort the array values from smallest to largest sort_order.
		$goto = array_flip($goto);								// [goto, sort_order] > [sort_order, goto].
		$key = array_shift($goto);								// Pop the first goto value.
		$_SESSION['chained_goto_queue'] = array_flip($goto);	// [sort_order, goto] > [goto, sort_order].
		
		/** 
		 * THIS IS WHERE WE HANDLE WHAT TO GIVE AS THE NEXT QUESTION. 
		 * */
		// #1 - No more questions to ask or current the current answer ends the Topic.
		if(empty($key) || $key == 'finalize') {
			return false;
		}
		
		// #2 - Select NEXT question in order after the current answer is selected.
		if($key == 'next') {
			$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d AND sort_order > %d ORDER BY sort_order LIMIT 1", $question->quiz_id, $question->sort_order));
			return $question;	
		}
	
		// #3 - Numeric keys means go to the direct question.
		if(is_numeric($key)) {
			$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE quiz_id=%d AND id=%d LIMIT 1", $question->quiz_id, $key));
			return $question;
		}
		
		// just in case
		return false;		
	}

}