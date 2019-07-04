<div class="wrap">
	<h1><?php printf(__('Manage Questions for the %s Algorithm', 'chained'), $quiz->title);?> </h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">
	
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Algorithms', 'chained')?></a>
			| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
			| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Algorithm', 'chained')?></a>
		</p>
		<p><a href="admin.php?page=chainedquiz_questions&action=add&quiz_id=<?php echo $quiz->id?>"><?php _e('New Question', 'chained')?></a> 
		| <a href="#" onclick="jQuery('#hide_answers').toggle();jQuery('#show_answers').toggle();return false;"><?php _e('SHOW/HIDE Answers', 'chained')?></a></p>

		<?php if(sizeof($questions)):?>
			<table class="widefat" id="hide_answers">
				<tr><th>#</th><th><?php _e('ID', 'chained')?></th><th><?php _e('Question', 'chained')?></th><th><?php _e('Type', 'chained')?></th>
					<th><?php _e('Edit / Delete', 'chained')?></th></tr>
				<?php foreach($questions as $cnt=>$question):
					$class = ('alternate' == @$class) ? '' : 'alternate';?>
					<tr class="<?php echo $class?>">
						<td><?php if($count > 1 and $cnt):?>
							<a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>&move=<?php echo $question->id?>&dir=up"><img src="<?php echo CHAINED_URL."/img/arrow-up.png"?>" alt="<?php _e('Move Up', 'hostelpro')?>" border="0"></a>
						<?php else:?>&nbsp;<?php endif;?>
						<?php if($count > $cnt+1):?>	
							<a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>&move=<?php echo $question->id?>&dir=down"><img src="<?php echo CHAINED_URL."/img/arrow-down.png"?>" alt="<?php _e('Move Down', 'hostelpro')?>" border="0"></a>
						<?php else:?>&nbsp;<?php endif;?></td>					
						<td><?php echo $question->id?></td>
						<td><?php echo stripslashes($question->title)?></td>
						<td><?php echo $question->qtype?></td>
						<td><a href="admin.php?page=chainedquiz_questions&action=edit&id=<?php echo $question->id?>"><?php _e('Edit', 'chained')?></a> | <a href="#" onclick="chainedConfirmDelete(<?php echo $question->id?>);return false;"><?php _e('Delete', 'chained')?></a></td>
					</tr>
				<?php endforeach;?>	
			</table>

			<table class="widefat" id="show_answers" style="display: none;">
				<tr><th>#</th><th><?php _e('ID', 'chained')?></th><th><?php _e('Question', 'chained')?></th><th><?php _e('Type', 'chained')?></th>
					<th><?php _e('Edit / Delete', 'chained')?></th></tr>
				<?php foreach($questions as $cnt=>$question):
					$class = ('alternate' == @$class) ? '' : 'alternate';?>
					<tr class="<?php echo $class?>">
						<td><?php if($count > 1 and $cnt):?>
							<a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>&move=<?php echo $question->id?>&dir=up"><img src="<?php echo CHAINED_URL."/img/arrow-up.png"?>" alt="<?php _e('Move Up', 'hostelpro')?>" border="0"></a>
						<?php else:?>&nbsp;<?php endif;?>
						<?php if($count > $cnt+1):?>	
							<a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>&move=<?php echo $question->id?>&dir=down"><img src="<?php echo CHAINED_URL."/img/arrow-down.png"?>" alt="<?php _e('Move Down', 'hostelpro')?>" border="0"></a>
						<?php else:?>&nbsp;<?php endif;?></td>					
						<td><?php echo $question->id?></td><td><?php echo stripslashes($question->title)?></td>
						<td><?php echo $question->qtype?></td>
						<td><a href="admin.php?page=chainedquiz_questions&action=edit&id=<?php echo $question->id?>"><?php _e('Edit', 'chained')?></a> | <a href="#" onclick="chainedConfirmDelete(<?php echo $question->id?>);return false;"><?php _e('Delete', 'chained')?></a></td>
					</tr>
					<tr class="<?php echo $class?>">
						<td colspan="5">
							<table>
								<?php foreach($choices as $choice): ?>
									<?php if ($choice->question_id == $question->id): ?>
										<tr>
											<td width="500px"><?php echo $choice->choice ?></td>
											<?php if(intval($choice->goto)): ?>
												<td style="width:100px;"><a href="admin.php?page=chainedquiz_questions&action=edit&id=<?php echo $choice->goto?>"><?php echo $choice->goto ?></a></td>
											<?php else: ?>
												<td style="width:100px;"><?php echo $choice->goto ?></td>
											<?php endif;?>
										</tr>
									<?php endif;?>

								<?php endforeach;?>
							</table>
						</td>
					</tr>
				<?php endforeach;?>	
			</table>
			
			<h3>Did you know?</h3>
			<p>You can easily clone any Algorithm by creating a copy of the Algorithm.</p>
		<?php endif;?>
	
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>
</div>

<script type="text/javascript" >
function chainedConfirmDelete(qid) {
	if(confirm("<?php _e('Are you sure?', 'chained')?>")) {
		window.location = 'admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>&del=1&id='+qid;
	}
}
</script>