<div class="wrap">
	<h1><?php _e('Add/Edit Algorithm', 'chained')?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">
	
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Algorithms', 'chained')?></a>
		<?php if(!empty($quiz->id)):?>
			| <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Questions', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results/Outcomes', 'chained')?></a>
		<?php endif;?></p>
		
		<form method="post" onsubmit="return validateChainedQuiz(this);">
			<p><label><?php _e('Algorithm Name', 'chained')?></label> <input type="text" name="title" size="60" value="<?php echo stripslashes(@$quiz->title)?>"></p>
			<h2><?php _e('Algorithm Settings', 'chained')?></h2>
				
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
					<?php _e('Store Algorithm URL.', 'chained');?>
				</p>				
				<?php if(!$is_published):?>
					<p><input type="checkbox" name="auto_publish" value="1"> <?php _e('Post results to the blog.', 'chained')?></p>
				<?php endif;?>


			<h2><?php _e('Finish Screen', 'chained')?></h2>
			<p><?php _e('The Finish Screen is displayed to the user after the last algorithm question was answered. The following ', 'chained')?>
			<strong><?php _e('Injection Codes', 'chained')?></strong> <?php _e(' will dynamically insert text from the algorithm for the user to see.', 'chained')?></p>
			
			<ul>
				<li>{{soap-note}}</li>				
				<li>{{questions}} <?php _e('- Total # of questions answered.', 'chained')?></li>
				<li>{{points}} <?php _e('- Points for all the answers.', 'chained')?></li>
				<li>{{result-title}} <?php _e('- The result (grade) title', 'chained')?></li>
				<li>{{result-text}} <?php _e('- The result (grade) text/description', 'chained')?></li>				
				<!-- (deprecating this injection code) <li>{{answers-table}} <?php _e('- A table with the questions, answers given by the user, correct / wrong info and points collected.', 'chained')?></li> -->				
				<!-- (let's leave this for the next version) li>{{correct}} <?php _e('- The number of correctly answered questions', 'chained')?></li> -->
			</ul>			
			<p><?php echo wp_editor(stripslashes($output), 'output')?></p>		
		
			
			<!-- (the original email output code)
			<p><input type="checkbox" name="email_admin" value="1" <?php if(!empty($quiz->email_admin)) echo 'checked'?> onclick="if(this.checked || this.form.email_user.checked) {jQuery('#chainedEmailSettings').show()} else {jQuery('#chainedEmailSettings').hide()};"> <?php _e('Send me email when user completes this Algorithm. It will be delivered to the email address from your main WP Settings page.', 'chained');?></p>
			<p><input type="checkbox" name="email_user" value="1" <?php if(!empty($quiz->email_user)) echo 'checked'?> onclick="if(this.checked || this.form.email_admin.checked) {jQuery('#chainedEmailSettings').show()} else {jQuery('#chainedEmailSettings').hide()};"> <?php _e('Send email to user with their result. If the user is not logged in visitor an optional "Enter email" field will automatically appear above the Algorithm.', 'chained');?></p>
			
			<div id="chainedEmailSettings" style="display:<?php echo (empty($quiz->email_admin) and empty($quiz->email_user)) ? 'none' : 'block'?>;">
				<p><input type="checkbox" name="set_email_output" value="1" <?php if(!empty($quiz->set_email_output)) echo 'checked'?> onclick="this.checked ? jQuery('#chainedEmailOutputs').show() : jQuery('#chainedEmailOutputs').hide();"> 
				<?php _e('Set email contents (if you skip this, the final screen output will be used for emails).', 'chained');?></p>
				
				<div id="chainedEmailOutputs" style="display:<?php echo empty($quiz->set_email_output) ? 'none' : 'email';?>">
					<?php echo wp_editor(stripslashes($quiz->email_output), 'email_output')?>
					<br />
					<p><?php _e('You can use the same variables as in the Final Output box', 'chained');?><br>
					<?php _e('By default this content is used for both the email sent to user, and the email sent to admin. You can however use the {{{split}}} tag to make the email contents different. The content before the {{{split}}} tag will be sent to the user and the content after the {{{split}}} tag - to the admin.','chained');?></p>
				</div>
			</div>
			-->

			<h2><?php _e('Finish Data', 'chained')?></h2>
			<p><?php _e('The data to be generated and saved for e-mail, provider, and dashboards. You can use the same Injection Codes as in the Final Screen box.', 'chained')?></p>

			<div id="chainedEmailSettings" style="display:block">
				<div id="chainedEmailOutputs" style="display:<?php echo empty($quiz->set_email_output) ? 'none' : 'email';?>">
					<?php echo wp_editor(stripslashes($quiz->email_output), 'email_output')?><br />
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