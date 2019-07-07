<div class="wrap">
	<h1><?php _e('Add/Edit Topic', 'chained')?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">
	
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Topics', 'chained')?></a>
		<?php if(!empty($quiz->id)):?>
			| <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Questions', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results/Outcomes', 'chained')?></a>
		<?php endif;?></p>
		
		<form method="post" onsubmit="return validateChainedQuiz(this);">
			<p><label><?php _e('Topic Name', 'chained')?></label> <input type="text" name="title" size="60" value="<?php echo stripslashes(@$quiz->title)?>"></p>
			<h2><?php _e('Topic Settings', 'chained')?></h2>
				
				<p> <!-- User Login -->
					<input type="checkbox" name="require_login" value="1" <?php if(!empty($quiz->require_login)) echo 'checked'?> onclick="this.checked ? jQuery('#timesToTake').show() : jQuery('#timesToTake').hide(); ">
					<?php _e('Login required.', 'chained');?>
					<span id="timesToTake" style="display:<?php echo empty($quiz->require_login) ? 'none' : 'inline';?>">
						<?php printf(__('Limit quiz attempts to %s. (Enter 0 for unlimited attempts.)', 'chained'), '<input type="text" size="3" name="times_to_take" value="'.(empty($quiz->times_to_take) ? 0 : $quiz->times_to_take).'">');?> 			
					</span>		
				</p>
				
				<p> <!-- Send Emails -->
					<input type="checkbox" name="email_admin" value="1" <?php if(!empty($quiz->email_admin)) echo 'checked'?>>
					<?php _e('E-mail Orchestra admin(s).', 'chained');?>
				</p>
				<p>
					<input type="checkbox" name="email_user" value="1" <?php if(!empty($quiz->email_user)) echo 'checked'?>>
					<?php _e('E-mail user.', 'chained');?>
				</p>

				<!-- Other Options -->
				<p>
					<input type="checkbox" name="save_source_url" value="1" <?php if(!empty($quiz->save_source_url)) echo 'checked'?>>
					<?php _e('Store Topic URL.', 'chained');?>
				</p>				
				<?php if(!$is_published):?>
					<p><input type="checkbox" name="auto_publish" value="1"> <?php _e('Post results to the blog.', 'chained')?></p>
				<?php endif;?>


			<h2><?php _e('Patient Output', 'chained')?></h2>
			<p><?php _e('Screen displayed to the patient upon completion of the Topic. The following ', 'chained')?>
			<strong><?php _e('Injection Codes', 'chained')?></strong><?php _e(' will dynamically insert text from the Topic for the user to see.', 'chained')?></p>
			<ul>
				<li>{{patient-note}} <?php _e('- Notes about the Topic Answers for the patient.', 'chained')?></li>
				<li>{{soap-note}} <?php _e('- The SOAP note for providers.', 'chained')?></li>
				<li>{{questions}} <?php _e('- Total # of questions answered.', 'chained')?></li>
				<li>{{points}} <?php _e('- Points for all the answers.', 'chained')?></li>
				<li>{{result-title}} <?php _e('- The result (grade) title', 'chained')?></li>
				<li>{{result-text}} <?php _e('- The result (grade) text/description', 'chained')?></li>				
				<!-- (deprecating this injection code) <li>{{answers-table}} <?php _e('- A table with the questions, answers given by the user, correct / wrong info and points collected.', 'chained')?></li> -->				
				<!-- (let's leave this for the next version) li>{{correct}} <?php _e('- The number of correctly answered questions', 'chained')?></li> -->
			</ul>
			<p><?php echo wp_editor(stripslashes($output), 'output', ["textarea_rows" => 10])?></p>		

			<h2><?php _e('Provider Output', 'chained')?></h2>
			<p><?php _e('Screen displayed to a clinician (e.g., provider) upon completion of the Topic. The following ', 'chained')?>
			<strong><?php _e('Injection Codes', 'chained')?></strong><?php _e(' will dynamically insert text from the Topic for the user to see.', 'chained')?></p>
			<ul>
				<li>{{patient-note}} <?php _e('- Notes about the Topic Answers for the patient.', 'chained')?></li>
				<li>{{soap-note}} <?php _e('- The SOAP note for providers.', 'chained')?></li>				
				<li>{{questions}} <?php _e('- Total # of questions answered.', 'chained')?></li>
				<li>{{points}} <?php _e('- Points for all the answers.', 'chained')?></li>
				<li>{{result-title}} <?php _e('- The result (grade) title', 'chained')?></li>
				<li>{{result-text}} <?php _e('- The result (grade) text/description', 'chained')?></li>				
				<!-- (deprecating this injection code) <li>{{answers-table}} <?php _e('- A table with the questions, answers given by the user, correct / wrong info and points collected.', 'chained')?></li> -->				
				<!-- (let's leave this for the next version) li>{{correct}} <?php _e('- The number of correctly answered questions', 'chained')?></li> -->
			</ul>

			<div id="chainedEmailSettings" style="display:block">
				<div id="chainedEmailOutputs" style="display:email">
					<?php echo wp_editor(stripslashes($quiz->email_output), 'email_output', ["textarea_rows" => 10])?><br />
					<p><input type="checkbox" name="set_email_output" value="1" <?php if(!empty($quiz->set_email_output)) echo 'checked'?>> 
					<?php _e('Uncheck to use Finish Screen output instead.', 'chained');?></p>
					<p><?php _e('Use the {{{split}}} tag to make the email contents different for Admins and Users. The content before the {{{split}}} tag will be sent to the user and the content after the {{{split}}} tag - to the admin.','chained');?></p>
				</div>
			</div>
			
			
			
			<p><input type="submit" value="<?php _e('SAVE', 'chained')?>" class="button-primary"></p>
			<input type="hidden" name="ok" value="1">
			<?php wp_nonce_field('chained_quiz');?>
		</form>
		
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>	
</div>

<script type="text/javascript" >
function validateChainedQuiz(frm) {
	if(frm.title.value == '') {
		alert("<?php _e('Title is required', 'chained')?>");
		frm.title.focus();
		return false;
	}
	
	return true;
}
</script>