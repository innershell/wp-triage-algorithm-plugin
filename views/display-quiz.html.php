<?php // requires login?
if(!empty($quiz->require_login) and !is_user_logged_in()) {
	 echo "<p><b>".__('ACCESS DENIED: Please login for access to this Algorithm.', 'chained') . 
		      	"<p><a href='".wp_login_url($_SERVER["REQUEST_URI"])."'>".__('Click Here to Login', 'chained')."</a></p>";
		      if(get_option("users_can_register")) {
						echo " ".__('or', 'chained')." <a href='".site_url("/wp-login.php?watu_register=1&action=register&redirect_to=".urlencode(get_permalink( $post->ID )))."'>".__('Register', 'chained')."</a></b>";        
					}
					echo "</p>";
	return false;
}
// can re-take?
if(!empty($quiz->require_login) and !empty($quiz->times_to_take)) {
	$cnt_takings=$wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".CHAINED_COMPLETED."
				WHERE quiz_id=%d AND user_id=%d", $quiz->id, $user_ID)); 
	
	// multiple times allowed, but number is specified	
	if($quiz->times_to_take and $cnt_takings >= $quiz->times_to_take) {
		echo "<p><b>";
		printf(__("Sorry, you can take this quiz only %d times.", 'chained'), $quiz->times_to_take);
		echo "</b></p>";
		return false;
	}			
}
?>
<?php if(!empty($first_load)):?><div class="chained-quiz" id="chained-quiz-div-<?php echo $quiz->id?>"><?php endif;?>
<form method="post" id="chained-quiz-form-<?php echo $quiz->id?>">
	<div class="chained-quiz-area" id="chained-quiz-wrap-<?php echo $quiz->id?>">
		<?php if(!empty($quiz->email_user) and !is_user_logged_in()):?>
			<div class="chained-quiz-email">
				<p><input type="hidden" name="chained_email" value="<?php echo @$_POST['chained_email']?>" placeholder="<?php _e('Your email address:', 'chained');?>"></p>
			</div>
		<?php endif;?> 
		<div class="chained-quiz-question" id="chained-quiz-question-<?php echo $question->id?>">
			<?php echo $_question->display_question($question);?>
		</div>
		
		<div class="chained-quiz-choices" id="chained-quiz-choices-<?php echo $question->id?>">
				<?php echo $_question->display_choices($question, $choices);?>
		</div>
		
		<?php if(!empty($question->accept_comments)):?>
		<div class="chained-quiz-comments" id="chained-quiz-comments-<?php echo $question->id?>">
				<label><?php echo stripslashes($question->accept_comments_label);?></label>
				<input class='chained-quiz-frontend chained-quiz-comment' type='text' name='comments'>
		</div>
		<?php endif;?>
		
		<div class="chained-quiz-action">
			<input type="button" id="chained-quiz-action-<?php echo $quiz->id?>" value="<?php _e('Next', 'chained')?>" onclick="chainedQuiz.goon(<?php echo $quiz->id?>, '<?php echo admin_url('admin-ajax.php')?>');">
		</div>
	</div>
	<input type="hidden" name="question_id" value="<?php echo $question->id?>">
	<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
	<input type="hidden" name="post_id" value="<?php echo $post->ID?>">
	<input type="hidden" name="question_type" value="<?php echo $question->qtype?>">
	<input type="hidden" name="points" value="0">
</form>
<?php if(!empty($first_load)):?>
</div>
<script type="text/javascript" >
	jQuery(function(){
		chainedQuiz.initializeQuestion(<?php echo $quiz->id?>);	
	});

	// Deselects all checkboxes except for the one that was clicked.
	function deselectAllCheckboxes(choiceID) {
		jQuery('input:checkbox[value!=' + choiceID + ']').prop('checked',false);
	}

	// Deselects all checkboxes except for the one that was clicked.
	function deselectNoneCheckbox(choiceID) {
		jQuery('input:checkbox[value=' + choiceID + ']').prop('checked',false);
	}	
</script><?php endif;?>