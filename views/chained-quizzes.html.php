<div class="wrap">
	<h1><?php _e('Algorithms', 'chained')?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
	<p><a href="admin.php?page=chained_quizzes&action=add"><?php _e('New Algorithm', 'chained')?></a></p>
	
	<?php if(sizeof($quizzes)):?>
	<table class="widefat">
		<tr><th><?php _e('Algorithm Name', 'chained')?></th><th><?php _e('Shortcode', 'chained')?></th><th><?php _e('Questions', 'chained')?></th>
			<th><?php _e('Results', 'chained')?></th><th><?php _e('Submissions', 'chained')?></th><th><?php _e('Edit/Delete', 'chained')?></th></tr>
		<?php foreach($quizzes as $quiz):
			$class = ('alternate' == @$class) ? '' : 'alternate';?>
			<tr class="<?php echo $class?>"><td><?php if(!empty($quiz->post)) echo "<a href='".get_permalink($quiz->post->ID)."' target='_blank'>"; 
				echo stripslashes($quiz->title);
				if(!empty($quiz->post)) echo "</a>";?></td><td><input type="text" size="18" value="[chained-quiz <?php echo $quiz->id?>]" readonly onclick="this.select();"></td>
			<td><a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage', 'chained')?></a></td>
			<td><a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage', 'chained')?></a></td>
			<td><?php if($quiz->submissions):?>
				<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>"><?php printf(__('%d users', 'chained'), $quiz->submissions);?></a>
			<?php else: _e('No users', 'chained');
			endif;?>	</td>
			<td><a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit', 'chained')?></a>
			| <a href="admin.php?page=chained_quizzes&copy=1&id=<?php echo $quiz->id?>" onclick="return confirm('<?php _e('This will make a duplicate of the whole quiz.', 'chained')?>');"><?php _e('Copy', 'chained')?></a>
			| <a href="#" onclick="confirmDelQuiz(<?php echo $quiz->id?>);return false;"><?php _e('Delete', 'chained')?></a></td></tr>
		<?php endforeach;?>	
	</table>
	<p><?php _e('NOTE: If the Algorithm Name is not hyperlinked, it means the Algorithm shortcode is not published. To publish, insert the shortcode in a public Post or Page.', 'chained')?></p>
	
	<?php else:?>
		<p><?php _e('There are no quizzes yet.', 'chained')?></p>
	<?php endif;?>	
	
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>
</div>

<script type="text/javascript" >
function confirmDelQuiz(id) {
	if(confirm("<?php _e('Are you sure?', 'chained')?>")) {
		window.location = 'admin.php?page=chained_quizzes&del=1&id=' + id;
	}
}
</script>