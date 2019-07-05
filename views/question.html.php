<div class="wrap">
	<h1><?php printf(__('Add/Edit Question in "%s"', 'chained'), $quiz->title)?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">
	
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Algorithms', 'chained')?></a> | <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Back to Questions', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
			| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Algorithm', 'chained')?></a>
		</p>
		
		<form method="post" onsubmit="return chainedQuizValidate(this);">
			<p><label><?php _e('Question Title', 'chained')?></label> <input type="text" name="title" size="40" value="<?php echo @$question->title?>"></p>
			<p><label><?php _e('Question Contents', 'chained')?></label> <?php echo wp_editor(stripslashes(@$question->question), 'question', array('textarea_rows' => 3))?></p>
			
			<h3><?php _e('SOAP Note Type', 'chained')?></h3>
				<input type="radio" name="soap_type" value="n" <?php if(!empty($question->id) and $question->soap_type == 'n') echo 'checked'?>>None<br>
				<input type="radio" name="soap_type" value="s" <?php if(!empty($question->id) and $question->soap_type == 's') echo 'checked'?>>Subjective<br>
				<input type="radio" name="soap_type" value="o" <?php if(!empty($question->id) and $question->soap_type == 'o') echo 'checked'?>>Objective<br>				

			<h3><?php _e('Question Type', 'chained')?></h3>

			<p><label><select name="qtype" onchange="this.value == 'radio' ? jQuery('#chainedAutoContinue').show() : jQuery('#chainedAutoContinue').hide();">
				<option value="none" <?php if(!empty($question->id) and $question->qtype == 'none') echo 'selected'?>><?php _e('None (no answer required)','chained')?></option>
				<option value="radio" <?php if(!empty($question->id) and $question->qtype == 'radio') echo 'selected'?>><?php _e('Radio Buttons (choose one answer)','chained')?></option>
				<option value="checkbox" <?php if(!empty($question->id) and $question->qtype == 'checkbox') echo 'selected'?>><?php _e('Checkboxes (choose multiple answers)','chained')?></option>
				<!-- <option value="field" <?php if(!empty($question->id) and $question->qtype == 'field') echo 'selected'?>><?php _e('Field (single line of text)','chained')?></option> -->
				<option value="text" <?php if(!empty($question->id) and $question->qtype == 'text') echo 'selected'?>><?php _e('Text Box (multiple lines of text)','chained')?></option>
				<option value="date" <?php if(!empty($question->id) and $question->qtype == 'date') echo 'selected'?>><?php _e('Date (calendar to pick date)','chained')?></option>
			</select>
			
			<span id="chainedAutoContinue" style="display:<?php echo (empty($question->id) or $question->qtype == 'radio') ? 'inline' : 'none';?>"><input type="checkbox" name="autocontinue" value="1" <?php if(!empty($question->autocontinue)) echo 'checked'?>> <?php _e('Automatically continue to the next question when a choice is selected', 'chained')?></span> </p>
			
			<p><input type="checkbox" name="accept_comments" value="1" <?php if(!empty($question->accept_comments)) echo 'checked'?>> <?php _e('Accept comments along with the answer.', 'chained');?> &nbsp;
			<?php _e('Label before the comments field:', 'chained');?> <input type="text" name="accept_comments_label" size="30" value="<?php echo empty($question->accept_comments_label) ? __('Your comments:', 'chained') : stripslashes(@$question->accept_comments_label);?>"></p>
			
			<h3><?php _e('Answers and Settings', 'chained')?></h3>
			
			
			<div id="answerRows">
				<?php if(!empty($choices) and sizeof($choices)):
					foreach($choices as $choice):
						include(CHAINED_PATH."/views/choice.html.php");
					endforeach;
				endif;
				unset($choice);
				include(CHAINED_PATH."/views/choice.html.php");?>
			</div>

			<!-- Buttons -->
			<p>
				<input type="button" value="<?php _e('Add Row', 'chained')?>" onclick="chainedQuizAddChoice();" class="button">
				<input type="submit" value="<?php _e('SAVE','chained')?>" class="button-primary">
			</p>
			<input type="hidden" name="ok" value="1">
			<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
			<?php wp_nonce_field('chained_question');?>
		</form>
		
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>		
</div>

<script type="text/javascript" >
var numChoices = 1;
function chainedQuizAddChoice() {
	html = '<?php ob_start();
	include(CHAINED_PATH."/views/choice.html.php");
	$content = ob_get_clean();	
	$content = str_replace("\n", '', $content);
	echo $content; ?>';
	
	// the correct checkbox value
	numChoices++;
	html = html.replace('name="is_correct[]" value="1"', 'name="is_correct[]" value="'+numChoices+'"');
	
	jQuery('#answerRows').append(html);
}

function chainedQuizValidate(frm) {
	if(frm.title.value == '') {
		alert("<?php _e('Please enter question title', 'chained')?>");
		frm.title.focus();
		return false;
	}
	
	return true;
}
</script>