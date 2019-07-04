<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizQuestion {
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
			quiz_id=%d, question=%s, qtype=%s, soap_type=%s, rank=%d, title=%s, autocontinue=%d, sort_order=%d, 
			accept_comments=%d, accept_comments_label=%s", 
			intval($vars['quiz_id']), $vars['question'], $vars['qtype'], $vars['soap_type'], intval(@$vars['rank']), $vars['title'], 
			intval(@$vars['autocontinue']), $sort_order, $accept_comments, $accept_comments_label));
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return $wpdb->insert_id;	
	} // end add
	
	function save($vars, $id) {
		global $wpdb;
		
		$vars['title'] = sanitize_text_field($vars['title']);
		if(!in_array($vars['qtype'], array('radio', 'checkbox', 'field', 'text'))) $vars['qtype'] = 'none';
		if(!current_user_can('unfiltered_html')) {
			$vars['question'] = strip_tags($vars['question']);
		}
		
		$accept_comments = empty($vars['accept_comments']) ? 0 : 1;
		$accept_comments_label = sanitize_text_field($vars['accept_comments_label']);
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".CHAINED_QUESTIONS." SET
			question=%s, qtype=%s, soap_type=%s, title=%s, autocontinue=%d, accept_comments=%d, accept_comments_label=%s WHERE id=%d", 
			$vars['question'], $vars['qtype'], $vars['soap_type'], $vars['title'], intval(@$vars['autocontinue']), 
			$accept_comments, $accept_comments_label, $id));
			
			
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	
	function delete($id) {
		global $wpdb;
	
		// delete choices		
		$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_CHOICES." WHERE question_id=%d", $id));
		
		// delete question
		$result = $wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_QUESTIONS." WHERE id=%d", $id));
		
		if($result === false) throw new Exception(__('DB Error', 'chained'));
		return true;	
	}
	
	// saves the choices on a question
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
				$_POST['provider_note'.$choice->id] = strip_tags($_POST['provider_note'.$choice->id]);
				$_POST['assessment'.$choice->id] = strip_tags($_POST['assessment'.$choice->id]);
				$_POST['plan'.$choice->id] = strip_tags($_POST['plan'.$choice->id]);
			}

			$_POST['goto'.$choice->id] = sanitize_text_field($_POST['goto'.$choice->id]);
			//if(!is_numeric($_POST['points'.$choice->id])) $_POST['points'.$choice->id] = 0;  // Points deprecated from questions.
			
			// else update
			$wpdb->query($wpdb->prepare("UPDATE ".CHAINED_CHOICES." SET
				choice=%s, provider_note=%s, assessment=%s, plan=%s, is_correct=%d, goto=%s WHERE id=%d", 
				$_POST['answer'.$choice->id], $_POST['provider_note'.$choice->id], $_POST['assessment'.$choice->id], $_POST['plan'.$choice->id], 
				intval(@$_POST['is_correct'.$choice->id]), $_POST['goto'.$choice->id], $choice->id));
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
	} // end save_choices

	// displays the question contents
	function display_question($question) {
		// for now only add stripslashes and autop, we'll soon add a filter like in Watupro
		$content = stripslashes($question->question);
		//$content = wpautop($content);
		$content = do_shortcode($content);
		return $content;
	}

	// displays the possible choices on a question
	function display_choices($question, $choices) {
		$autocontinue = $output = '';
		if($question->qtype == 'radio' and $question->autocontinue) {
			$autocontinue = "onclick=\"chainedQuiz.goon(".$question->quiz_id.", '".admin_url('admin-ajax.php')."');\"";
			$output .= '<!--hide_go_ahead-->';
		}  	   
		
			switch($question->qtype) {
				case 'text':

					foreach($choices as $choice) {
						$output .= "
							<div class='chained-quiz-choice'>
								<input type='hidden' name='answers[]' value='".$choice->id."'>
								<textarea class='chained-quiz-frontend' required='required' name='answer_texts[]' placeholder='".$choice->choice."' rows='3' cols='40'></textarea>
							</div>";
					}
					

/* 					$output .= "<div class='chained-quiz-choice'>
						<input type='hidden' name='answer' value='".$choices[0]->id."'>
						<textarea class='chained-quiz-frontend' required='required' name='answer_text' rows='5' cols='80'></textarea>
					</div>"; */
				return $output;
				break;
				case 'radio':
				case 'checkbox':
					$type = $question->qtype;
					$name = ($question->qtype == 'radio') ? "answer": "answers[]";				
					
					foreach($choices as $choice) {
						$choice_text = stripslashes($choice->choice);
						$choice_text = do_shortcode($choice_text);
						
						$output .= "<div class='chained-quiz-choice'><label class='chained-quiz-label'><input class='chained-quiz-frontend chained-quiz-$type' type='$type' name='$name' value='".$choice->id."' $autocontinue> $choice_text</label></div>";
					}
					return $output;
					break;
			}
	} // end display_choices
  
	// calculate the points of a given answer
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
  
	// gets the next question in a quiz, depending on the given answer
	// $question = SELECT * FROM CHAINED_QUESTIONS WHERE id=%d
	// $answer = The CHOICE_ID of text/radio or array of CHOICE_IDs if checkbox.
	function next($question, $answer) {
 		global $wpdb; 	
 		
		// select answer(s)
		$goto = array();
		$answer_ids = array(0);
		
		// Answer was from a checkbox, so build an array of answer IDs.
		if(is_array($answer)) {
			foreach($answer as $ans) {
				 if(!empty($ans)) $answer_ids[] = $ans;
			}
		} 
		else {
			/* THIS COULD BE A BUG NOW 
			if($question->qtype == 'text') {
					$answer = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".CHAINED_CHOICES."
	  		  WHERE question_id=%d AND choice LIKE %s", $question->id, $answer));				
			} */
			
			if(!empty($answer)) $answer_ids[] = $answer; // radio buttons and text areas
		} 
		
		// Remove any answers that are non numeric.
		$answer_ids = chained_int_array($answer_ids);  /* THIS WAS A BUG BEFORE WITH CHECKBOXES */
		if(empty($answer_ids)) $answer_ids = array(0);
		
		// Fetch the choices config for the answers selected by the user.
		$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_CHOICES." 
			WHERE question_id=%d AND id IN (".implode(",", $answer_ids).") ", $question->id));
			
		foreach($choices as $choice) {
			if(isset($goto[$choice->goto])) {
				$goto[$choice->goto]++;
			} else {
				$goto[$choice->goto] = 1;
			}
		}
	  
		// now sort goto to figure out what's the top goto selection	
		arsort($goto);				// Sort the array values (not keys) in reverse order.
		$goto = array_flip($goto);	// Flips keys and values. If duplicate values, only the last value will be used as a key.
		$key = array_shift($goto);	// Pops the first element value of the array.
		
		//let's treat textareas in different way. If answer is not found, let's not finalize the quiz but go to next
		if($question->qtype == 'text' and empty($key)) $key = 'next';
		
		// echo $key.'x'; 
		if(empty($key) or $key == 'finalize') return false;
		
		if($key == 'next') {
			// select next question by sort_order
			$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." 
				WHERE quiz_id=%d AND sort_order > %d ORDER BY sort_order LIMIT 1", $question->quiz_id, $question->sort_order));
			return $question;	
		}
	
		if(is_numeric($key)) {
			$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." 
					WHERE quiz_id=%d AND id=%d LIMIT 1", $question->quiz_id, $key));
				return $question;	
		}
		
		// just in case
		return false;		
	} // end next()


	// gets the next best question in a quiz, depending on the given answer
	// $question = SELECT * FROM CHAINED_QUESTIONS WHERE id=%d
	// $answer = The CHOICE_ID of text/radio or array of CHOICE_IDs if checkbox.
	
	
	/**
	 * BUSINESS RULES:
	 * 1. If any of the chosen answer(s) has a 'goto' question_id (not 'next' or 'finalize'), return it. 
	 *    The answers need to be sorted by the choice_id though because that is the order it is displayed to the user.
	 * 2. Find all previous chosen answers whose goto has a question_id without a record in USER_ANSWER and return the one with the highest sort order.
	 * 3. If the chosen answer(s) goto is next, 
	 *    a. Find the last checkbox question answered. Use this to build the entire checkbox family tree.
	 *    b. For each question in the tree, take the answer's goto and put on the tree and mark the question complete.
	 *    c. While there are incomplete questions, repeat b until all questions have been inspected.
	 *    d. The next question is the biggest in the tree PLUS one.
	 */
	function next0($question, $answer) {
		global $wpdb;
		
		// Preparation: Create an array of user answers.
		$answer_ids = array(0);
		if(is_array($answer)) {										// This is for multi-select answers.
			foreach($answer as $ans) {
				 if(!empty($ans)) $answer_ids[] = $ans;
			}
		} else {													// This is for single-select answers.
			if(!empty($answer)) $answer_ids[] = $answer;
		}

		/* BUSINESS RULE #1 */
		// See if there is a goto question_id in any of the chosen answers.
		// Todo, might want to restrict the CHAINED_CHOICES query by question_id as well.
		if(!empty($answer_ids)) {
			$choice = $wpdb->get_row("SELECT * FROM ".CHAINED_CHOICES." WHERE id IN (".implode(",", $answer_ids).") AND goto <> 'next' ORDER BY id LIMIT 1");
			if ($wpdb->num_rows) $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUESTIONS." WHERE id=%d", $choice->goto));
			if ($wpdb->num_rows) return $question;
		}

		/* BUSINESS RULE #2 */
		// Get all the answers for this quiz provided by this user session.
		$ua_results = $wpdb->get_results($wpdb->prepare("SELECT question_id, answer FROM ".CHAINED_USER_ANSWERS," WHERE quiz_id=%d, completion_id=%d"), $question->quiz_id, intval($_SESSION['chained_completion_id']));
 		$ua_array = array();
		foreach($ua_results as $res) {
			if (intval($res->$answer)) {
				$ua_array[] = $res;
			} else {
				$ua_array = array_merge($ua_array, explode($res));
			}
		} 

		// Get all the possible answers and goto for this quiz.
		//$choice_results = $wpdb->get_results($wpdb->prepare("SELECT id, goto FROM ".CHAINED_CHOICES." WHERE quiz_id=%d AND goto NOT IN ('next','finalize') ORDER BY id DESC"), $question->quiz_id);
/* 		$choices_array = array();
		foreach ($choices_results as $res) {
			$choices_array[] = $res->goto;
		} */

		// Filter down to choices which have only been answered/selected by the user previously.



		// Get all the answers for this quiz which ARE in the answers table but the goto question_id is not in the answers tabe.
		// for loop here to build an array.
		


		// intersect the two arrays.
	   
	   // just in case
	   return false;		
   } // end next()

}