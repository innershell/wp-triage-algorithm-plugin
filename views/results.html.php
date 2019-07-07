<div class="wrap">
	<h1><?php _e('Manage Topic Results/Outcomes', 'chained')?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
		<!-- The menu area. -->
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Topics', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Questions', 'chained')?></a>
			| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Topic', 'chained')?></a>
		</p>
		
		<!-- Existing saved results. -->
		<?php foreach($results as $result):?>
			<form method="post" onsubmit="return validateChainedResult(this);">
				<p><label><?php _e('Result Title', 'chained')?></label> <input type="text" name="title" size="60" value="<?php echo $result->title?>"></p>
				<p><?php _e('Min. Points:', 'chained')?> <input type="text" size="4" name="points_bottom" value="<?php echo $result->points_bottom?>">&nbsp;&nbsp;&nbsp;
				<?php _e('Max. Points:', 'chained')?> <input type="text" size="4" name="points_top" value="<?php echo $result->points_top?>"></p>
				<p><label><?php _e('Result Description', 'chained')?></label> <?php echo wp_editor(stripslashes($result->description), 'description'.$result->id, array("textarea_rows" => 3))?></p>
				<div>
					<div class="one-line">
						<label><?php _e('Subjective Note', 'chained')?></label><br />
						<textarea rows="3" cols="40" name="subjective<?php echo $result->id?>"><?php echo stripslashes($result->subjective);?></textarea>
					</div>
					<div class="one-line">
						<label><?php _e('Objective Note', 'chained')?></label><br />
						<textarea rows="3" cols="40" name="objective<?php echo $result->id?>"><?php echo stripslashes($result->objective);?></textarea>
					</div>
				</div>
				<div>
					<div class="one-line">
						<label><?php _e('Assessment', 'chained')?></label><br />
						<textarea rows="3" cols="40" name="assessment<?php echo $result->id?>"><?php echo stripslashes($result->assessment);?></textarea>
					</div>
					<div class="one-line">
						<label><?php _e('Plan', 'chained')?></label><br />
						<textarea rows="3" cols="40" name="plan<?php echo $result->id?>"><?php echo stripslashes($result->plan);?></textarea>
					</div>
				</div>
				<p><label><?php _e('Optional redirect URL', 'chained')?></label> <input type="text" name="redirect_url" size="60" value="<?php echo $result->redirect_url?>"><br />
				<i><?php _e('If you enter this, the quiz will redirect to the URL instead of showing the "Final Output".', 'chained');?></i></p>
				<p><input type="submit" name="save" value="<?php _e('Save Result', 'chained')?>" class="button-primary">
				<input type="button" value="<?php _e('Delete Result', 'chained')?>" onclick="confirmDelChainedResult(this.form);" class="button"></p>
				<input type="hidden" name="id" value="<?php echo $result->id?>">
				<input type="hidden" name="del" value="0">
				<?php wp_nonce_field('chained_result');?>
			</form>
			<br />
		<?php endforeach;?>

		<!-- Add a new result. -->
		<form method="post" onsubmit="return validateChainedResult(this);">
			<p><label><?php _e('Result Title', 'chained')?></label> <input type="text" name="title" size="60"></p>
			<p><?php _e('Min. Points:', 'chained')?> <input type="text" size="4" name="points_bottom">&nbsp;&nbsp;&nbsp;
			<?php _e('Max. Points:', 'chained')?> <input type="text" size="4" name="points_top"></p>
			<p><label><?php _e('Result Description', 'chained')?></label> <?php echo wp_editor('', 'description', array("textarea_rows" => 3))?></p>
			<div>
				<div class="one-line">
					<label><?php _e('Subjective Note', 'chained')?></label><br />
					<textarea rows="3" cols="40" name="subjective"></textarea>
				</div>
				<div class="one-line">
					<label><?php _e('Objective Note', 'chained')?></label><br />
					<textarea rows="3" cols="40" name="objective"></textarea>
				</div>
			</div>
			<div>
				<div class="one-line">
					<label><?php _e('Assessment', 'chained')?></label><br />
					<textarea rows="3" cols="40" name="assessment"></textarea>
				</div>
				<div class="one-line">
					<label><?php _e('Plan', 'chained')?></label><br />
					<textarea rows="3" cols="40" name="plan"></textarea>
				</div>
			</div>
			<p><label><?php _e('Optional redirect URL', 'chained')?></label> <input type="text" name="redirect_url" size="60"><br />
			<i><?php _e('The Topic will redirect to the URL instead of showing the "Final Output".', 'chained');?></i></p>
			<p><input type="submit" name="add" value="<?php _e('Add Result', 'chained')?>" class="button-primary"></p>
			<?php wp_nonce_field('chained_result');?>
		</form>
	
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>
</div>

<script type="text/javascript" >
function validateChainedResult(frm) {	
	if(frm.title.value == '') {
		alert("<?php _e('Please enter title', 'chained')?>");
		frm.title.focus();
		return false;
	}
	
	return true;
}

function confirmDelChainedResult(frm) {
	if(confirm("<?php _e('Are you sure?', 'chained')?>")) {
		frm.del.value=1;
		frm.submit();
	}
} 
</script>