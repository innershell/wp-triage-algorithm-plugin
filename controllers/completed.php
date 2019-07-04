<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedQuizCompleted {
	static function manage() {
		global $wpdb;
		
		// select quiz
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", intval($_GET['quiz_id'])));
		$ob = empty($_GET['ob']) ? 'tC.id' : $_GET['ob'];
		if(!in_array($ob, array('tC.id', 'datetime', 'points', 'result_title'))) $ob = 'tC.id';
		$dir = (empty($_GET['dir']) or $_GET['dir'] == 'desc')  ? 'desc' : 'asc';
		
		
		// select completed records, paginate by 50
		$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
		$limit_sql = empty($_GET['chained_export']) ? "LIMIT $offset, 25" : ""; 
		
		if(!empty($_GET['del'])) {
			$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_COMPLETED." WHERE id=%d", intval($_GET['del'])));
		}		
		
		if(!empty($_POST['cleanup_all']) and check_admin_referer('chained_cleanup')) {
			$wpdb->query($wpdb->prepare("DELETE FROM ".CHAINED_COMPLETED." WHERE quiz_id=%d", $quiz->id));
			chained_redirect("admin.php?page=chainedquiz_list&quiz_id=".$quiz->id);	 
		}
		
		// filter / search?
		$filters = $joins = array();	
		$filter_sql = "";
		
		// display name
		$dn = '';
		if(!empty($_GET['dn'])) {
		   $dn = sanitize_text_field($_GET['dn']);
			switch($_GET['dnf']) {
				case 'contains': $like = "%$dn%"; break;
				case 'starts': $like = "$dn%"; break;
				case 'ends': $like = "%$dn"; break;
				case 'equals':
				default: $like = $dn; break;			
			}
			
			$filters[]= " display_name LIKE '$like' ";
		}
		
		// email
		$email = '';
		if(!empty($_GET['email'])) {
		   $email = sanitize_email($_GET['email']); 
			switch($_GET['emailf']) {
				case 'contains': $like="%$email%"; break;
				case 'starts': $like="$email%"; break;
				case 'ends': $like="%$email"; break;
				case 'equals':
				default: $like=$email; break;			
			}
			
			$filters[] = $wpdb->prepare(" user_email LIKE %s ", $like);
			//$filters[]=$wpdb->prepare(" ((user_id=0 AND email LIKE %s) OR (user_id!=0 AND user_email LIKE %s)) ", $like, $like);
			//$left_join = 'LEFT'; // when email is selected, do left join because it might be without logged user
		}
		
		// IP
		$ip = '';
		if(!empty($_GET['ip'])) {
		   $ip = sanitize_text_field($_GET['ip']);
			switch($_GET['ipf']) {
				case 'contains': $like="%$ip%"; break;
				case 'starts': $like="$ip%"; break;
				case 'ends': $like="%$ip"; break;
				case 'equals':
				default: $like=$ip; break;			
			}
			
			$filters[]=$wpdb->prepare(" ip LIKE %s ", $like);
		}
		
		// Date
		$date = '';
		if(!empty($_GET['date'])) {
		   $date = sanitize_text_field($_GET['date']);
			switch($_GET['datef']) {
				case 'after': $filters[]=$wpdb->prepare(" DATE(datetime) > %s ", $date); break;
				case 'before': $filters[]=$wpdb->prepare(" DATE(datetime) < %s ", $date); break;
				case 'equals':
				default: $filters[]=$wpdb->prepare(" DATE(datetime)=%s ", $date); break;
			}
		}
		
		// Points
		$points = '';
		if(!empty($_GET['points'])) {
		   $points = floatval($_GET['points']);
			switch($_GET['pointsf']) {
				case 'less': $filters[]=$wpdb->prepare(" points < %f ", $points); break;
				case 'more': $filters[]=$wpdb->prepare(" points > %f ", $points); break;
				case 'equals':
				default: $filters[]=$wpdb->prepare(" points=%f ", $points); break;
			}
		}
		
		// result
		if(!empty($_GET['result_id'])) {
		   $_GET['result_id'] = intval($_GET['result_id']);
			$filters[] = $wpdb->prepare(" result_id=%d ", $_GET['result_id']); 
		
		}
		
		// construct filter & join SQLs
		if(count($filters)) {
			$filter_sql=" AND ".implode(" AND ", $filters);
		}
					
		$records = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS tC.*, tU.user_nicename as user_nicename, tR.title as result_title
			FROM ".CHAINED_COMPLETED." tC LEFT JOIN ".CHAINED_RESULTS." tR ON tR.id = tC.result_id
			LEFT JOIN {$wpdb->users} tU ON tU.ID = tC.user_id
			WHERE tC.quiz_id=".$quiz->id." AND tC.not_empty=1 $filter_sql 
			ORDER BY $ob $dir $limit_sql");
			
		$count = $wpdb->get_var("SELECT FOUND_ROWS()"); 	
		
		// select all the given answers in these records
		$rids = array(0);
		foreach($records as $record) $rids[] = $record->id;		
		$answers = $wpdb->get_results( "SELECT tA.answer as answer, tA.points as points, tQ.title as question,
			tA.completion_id as completion_id, tQ.qtype as qtype, tA.comments as comments 
			FROM ".CHAINED_USER_ANSWERS." tA JOIN ".CHAINED_QUESTIONS." tQ
			ON tQ.id = tA.question_id
			WHERE tA.completion_id IN (" .implode(',', $rids). ") ORDER BY tA.id" ); 
			
		// now for the answers we need to match the textual values of what the user has answered
		$aids = array(0);
		foreach($answers as $answer) {
			$ids = explode(',', $answer->answer);
			
			foreach($ids as $id) {
				if(!empty($id) and !in_array($id, $aids)) $aids[] = $id;
			}
		}	
		
		$aids = chained_int_array($aids);
		
		$choices = $wpdb->get_results("SELECT id, choice FROM ".CHAINED_CHOICES." WHERE id IN (" . implode(',', $aids) . ")");
		
		// now do the match
		foreach($answers as $cnt => $answer) {
			$ids = explode(',', $answer->answer);
			$answer_text = '';
			
			if($answer->qtype == 'text') $answer_text = esc_html(stripslashes($answer->answer));
			else { 
				foreach($ids as $id) {
					foreach($choices as $choice) {
						if($choice->id == $id) {
							if(!empty($answer_text)) $answer_text .= ", ";
							$answer_text .= stripslashes($choice->choice);
						}
					} // end foreach choice
				} // end foreach id
			} // end if not textarea	
			
			// add comments if any
			if(!empty($answer->comments)) $answer_text .= '<p>'.stripslashes($answer->comments).'</p>';			
			
			$answers[$cnt]->answer_text = $answer_text;
		} // end foreach answer
		
		// now match the answers to records
		foreach($records as $cnt=>$record) {
			$record_answers = array();
			
			foreach($answers as $answer) {
				if($record->id == $answer->completion_id) $record_answers[] = $answer;
			}
			
			$records[$cnt] -> details = $record_answers;
		}
		
		$dateformat = get_option('date_format');
		$timeformat = get_option('time_format');
		
		if(!empty($_GET['chained_export'])) {
			$newline = kiboko_define_newline();		
			$delim = get_option('chained_csv_delim');
			if(empty($delim) or !in_array($delim, array(",", "tab", ';'))) $delim = ",";
			if($delim == 'tab') $delim = "\t";
			$quote = get_option('chained_csv_quotes');
			if(empty($quote)) $quote = ''; 
			else $quote = '"';
			
			$csv = "";
			$rows=array();
			$titlerow =__("Record ID", 'chained').$delim.__("User name or IP", 'chained').$delim.
				__("Date / time", 'chained').$delim.__("Points", 'chained').$delim.__("Result", 'chained');
			if(!empty($_GET['details'])) {
				$titlerow .= $delim . __('Details');
			}	
			$rows[] = $titlerow;	
			foreach($records as $record) {
				$row = $record->id . $delim . (empty($record->user_id) ? $record->ip : $record->user_nicename) 
					. $delim . date_i18n($dateformat.' '.$timeformat, strtotime($record->datetime)) 
					. $delim . $record->points . $delim . $quote . stripslashes($record->result_title) . $quote;
					
				if(!empty($_GET['details'])) {
					$details = '';
					if(count($record->details)) {						
						foreach($record->details as $n => $d) {
							if($n) $details .= "\n";
							$details .= stripslashes($d->question).": ".stripslashes($d->answer_text).' '.sprintf(__('(%d points)', 'chained'), $d->points);
						}
					}					
					
					$details = str_replace('"', "'", $details); // replace double quotes
					$details = str_replace($delim, "    ", $details); // replace delimiter 
					$row .= $delim.$quote.$details.$quote;
				}					
				$rows[] = $row;		
			} // end foreach taking
			$csv = implode($newline,$rows);		
			
			$now = gmdate('D, d M Y H:i:s') . ' GMT';	
			$filename = 'quiz-'.$quiz->id.'-results.csv';	
			header('Content-Type: ' . kiboko_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Pragma: no-cache');
			echo $csv;
			exit;
		}	
		
		// this var will be added to links at the view
		$filters_url="dn=".$dn."&dnf=".sanitize_text_field(@$_GET['dnf'])."&email=".$email."&emailf=".
		sanitize_text_field(@$_GET['emailf'])."&ip=".$ip."&ipf=".sanitize_text_field(@$_GET['ipf'])."&date=".$date.
		"&datef=".sanitize_text_field(@$_GET['datef'])."&points=".$points."&pointsf=".sanitize_text_field(@$_GET['pointsf']).
		"&grade_id=".intval(@$_GET['grade_id'])."&source_url=".esc_url_raw(@$_GET['source_url']);		
		
		$display_filters = (!count($filters)) ? false : true;	
		
		// results (grades)
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." WHERE quiz_id=%d ORDER BY id", $quiz->id));
			
		include(CHAINED_PATH."/views/completed.html.php");
	} // end manage
	
	// defines whether to sort by ASC or DESC
	static function define_dir($col, $ob, $dir) {		
		if($ob != $col) return $dir;
		
		// else reverse
		if($dir == 'asc') return 'desc';
		else return 'asc'; 
	}
}