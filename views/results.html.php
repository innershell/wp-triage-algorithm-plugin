<div class="wrap">
	<h1><?php _e('Manage Algorithm Results/Outcomes', 'chained')?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Algorithms', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Questions', 'chained')?></a>
			| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Algorithm', 'chained')?></a>
		</p>
		
		<form method="post" onsubmit="return validateChainedResult(this);">
			<p><label><?php _e('Result title', 'chained')?></label> <input type="text" name="title" size="60"></p>
			<p><label><?php _e('Result text/description', 'chained')?></label> <?php echo wp_editor('', 'description')?></p>
			<p><?php _e('Min. points:', 'chained')?> <input type="text" size="4" name="points_bottom"> 
			<?php _e('Max. points:', 'chained')?> <input type="text" size="4" name="points_top"></p>
			<p><label><?php _e('Optional redirect URL', 'chained')?></label> <input type="text" name="redirect_url" size="60"><br />
			<i><?php _e('The Algorithm will redirect to the URL instead of showing the "Final Output".', 'chained');?></i></p>
			<p><input type="submit" name="add" value="<?php _e('Add Result', 'chained')?>" class="button-primary"></p>
			<?php wp_nonce_field('chained_result');?>
		</form>
		
		<?php foreach($results as $result):?>
		<hr>
		<form method="post" onsubmit="return validateChainedResult(this);">
			<p><label><?php _e('Result title', 'chained')?></label> <input type="text" name="title" size="60" value="<?php echo $result->title?>"></p>
			<p><label><?php _e('Result text/description', 'chained')?></label> <?php echo wp_editor(stripslashes($result->description), 'description'.$result->id)?></p>
			<p><?php _e('Min. points:', 'chained')?> <input type="text" size="4" name="points_bottom" value="<?php echo $result->points_bottom?>"> 
			<?php _e('Max. points:', 'chained')?> <input type="text" size="4" name="points_top" value="<?php echo $result->points_top?>"></p>
			<p><label><?php _e('Optional redirect URL', 'chained')?></label> <input type="text" name="redirect_url" size="60" value="<?php echo $result->redirect_url?>"><br />
			<i><?php _e('If you enter this, the quiz will redirect to the URL instead of showing the "Final Output".', 'chained');?></i></p>
			<p><input type="submit" name="save" value="<?php _e('Save Result', 'chained')?>" class="button-primary">
			<input type="button" value="<?php _e('Delete Result', 'chained')?>" onclick="confirmDelChainedResult(this.form);" class="button"></p>
			<input type="hidden" name="id" value="<?php echo $result->id?>">
			<input type="hidden" name="del" value="0">
			<?php wp_nonce_field('chained_result');?>
		</form>
		<?php endforeach;?>
	
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