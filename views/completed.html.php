<div class="wrap">
	<h1><?php printf(__('Submissions for Algorithm: "%s"', 'chained'), $quiz->title)?></h1>
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to Algorithms', 'chained')?></a> | <a href="admin.php?page=chainedquiz_questions&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Questions', 'chained')?></a>
		| <a href="admin.php?page=chainedquiz_results&quiz_id=<?php echo $quiz->id?>"><?php _e('Manage Results', 'chained')?></a>
		| <a href="admin.php?page=chained_quizzes&action=edit&id=<?php echo $quiz->id?>"><?php _e('Edit This Algorithm', 'chained')?></a>
	</p>
		
	<?php if(count($records) or $display_filters):?>
		<p>
			<a href="#" onclick="jQuery('#filterForm').toggle('slow');return false;"><?php _e('Search Filter', 'chained')?></a> |		
			<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&chained_export=1&noheader=1&<?php echo $filters_url;?>"><?php _e('Export CSV', 'chained')?></a> | 
			<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&chained_export=1&noheader=1&details=1&<?php echo $filters_url;?>"><?php _e('Export with Details', 'chained')?></a>
			<p><?php printf(__('Export files are currently delimited by "%s" as configured in the <a href="%s">Triage Algorithm Settings</a> page.', 'chained'), get_option('chained_csv_delim'), 'admin.php?page=chainedquiz_options');?></p>
		</p>
		
			
			<div id="filterForm" style="display:<?php echo $display_filters?'block':'none';?>;margin-bottom:10px;padding:5px;" class="widefat">
			<form method="get" class="chained-form" action="admin.php">
			<input type="hidden" name="page" value="chainedquiz_list">
			<input type="hidden" name="quiz_id" value="<?php echo $quiz->id?>">
				<div><label><?php _e('Username', 'chained')?></label> <select name="dnf">
					<option value="equals" <?php if(empty($_GET['dnf']) or $_GET['dnf']=='equals') echo "selected"?>><?php _e('Equals', 'chained')?></option>
					<option value="starts" <?php if(!empty($_GET['dnf']) and $_GET['dnf']=='starts') echo "selected"?>><?php _e('Starts with', 'chained')?></option>
					<option value="ends" <?php if(!empty($_GET['dnf']) and $_GET['dnf']=='ends') echo "selected"?>><?php _e('Ends with', 'chained')?></option>
					<option value="contains" <?php if(!empty($_GET['dnf']) and $_GET['dnf']=='contains') echo "selected"?>><?php _e('Contains', 'chained')?></option>
				</select> <input type="text" name="dn" value="<?php echo $dn?>"></div>
				<div><label><?php _e('Email', 'chained')?></label> <select name="emailf">
					<option value="equals" <?php if(empty($_GET['emailf']) or $_GET['emailf']=='equals') echo "selected"?>><?php _e('Equals', 'chained')?></option>
					<option value="starts" <?php if(!empty($_GET['emailf']) and $_GET['emailf']=='starts') echo "selected"?>><?php _e('Starts with', 'chained')?></option>
					<option value="ends" <?php if(!empty($_GET['emailf']) and $_GET['emailf']=='ends') echo "selected"?>><?php _e('Ends with', 'chained')?></option>
					<option value="contains" <?php if(!empty($_GET['emailf']) and $_GET['emailf']=='contains') echo "selected"?>><?php _e('Contains', 'chained')?></option>
				</select> <input type="text" name="email" value="<?php echo $email?>"></div>
				<div><label><?php _e('IP Address', 'chained')?></label> <select name="ipf">
					<option value="equals" <?php if(empty($_GET['ipf']) or $_GET['ipf']=='equals') echo "selected"?>><?php _e('Equals', 'chained')?></option>
					<option value="starts" <?php if(!empty($_GET['ipf']) and $_GET['ipf']=='starts') echo "selected"?>><?php _e('Starts with', 'chained')?></option>
					<option value="ends" <?php if(!empty($_GET['ipf']) and $_GET['ipf']=='ends') echo "selected"?>><?php _e('Ends with', 'chained')?></option>
					<option value="contains" <?php if(!empty($_GET['ipf']) and $_GET['ipf']=='contains') echo "selected"?>><?php _e('Contains', 'chained')?></option>
				</select> <input type="text" name="ip" value="<?php echo $ip?>"></div>
				<div><label><?php _e('Date Taken', 'watu')?></label> <select name="datef">
					<option value="equals" <?php if(empty($_GET['datef']) or $_GET['datef']=='equals') echo "selected"?>><?php _e('Equals', 'chained')?></option>
					<option value="before" <?php if(!empty($_GET['datef']) and $_GET['datef']=='before') echo "selected"?>><?php _e('Is before', 'chained')?></option>
					<option value="after" <?php if(!empty($_GET['datef']) and $_GET['datef']=='after') echo "selected"?>><?php _e('Is after', 'chained')?></option>			
				</select> <input type="text" name="date" value="<?php echo $date?>"> <i>YYYY-MM-DD</i></div>
				<div><label><?php _e('Points received', 'chained')?></label> <select name="pointsf">
					<option value="equals" <?php if(empty($_GET['pointsf']) or $_GET['pointsf']=='equals') echo "selected"?>><?php _e('Equal', 'chained')?></option>
					<option value="less" <?php if(!empty($_GET['pointsf']) and $_GET['pointsf']=='less') echo "selected"?>><?php _e('Are less than', 'chained')?></option>
					<option value="more" <?php if(!empty($_GET['pointsf']) and $_GET['pointsf']=='more') echo "selected"?>><?php _e('Are more than', 'chained')?></option>			
				</select> <input type="text" name="points" value="<?php echo $points?>"></div>
				
				<div><label><?php _e('Result equals:', 'chained')?></label> <select name="result_id">
						<option value="0"><?php _e('- Any result -', 'chained')?></option>
						<?php foreach($results as $result):?>
							<option value="<?php echo $result->id?>" <?php if(!empty($_GET['result_id']) and $_GET['result_id'] == $result->id) echo 'selected'?>><?php echo stripslashes($result->title)?></option>
						<?php endforeach;?>	
				</select> </div>
						
				<div><input type="submit" value="<?php _e('Search/Filter', 'chained')?>">
				<input type="button" value="<?php _e('Clear Filters', 'chained')?>" onclick="window.location='admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id;?>';"></div>
			</form>
			</div>	
		
		
		<table class="widefat">
			<tr><th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=tC.id&dir=<?php echo self :: define_dir('tC.id', $ob, $dir);?>&<?php echo $filters_url;?>"><?php _e('Record ID','chained')?></a></th><th><?php _e('User name, email or IP','chained')?></th><th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=datetime&dir=<?php echo self :: define_dir('datetime', $ob, $dir);?>&<?php echo $filters_url;?>"><?php _e('Date/time','chained')?></a></th>
			<th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=points&dir=<?php echo self :: define_dir('points', $ob, $dir);?>&<?php echo $filters_url;?>"><?php _e('Points','chained')?></a></th><th><a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&ob=result_title&dir=<?php echo self :: define_dir('result_title', $ob, $dir);?>&<?php echo $filters_url;?>"><?php _e('Result','chained')?></a></th>
			<th><?php _e('Delete', 'chained')?></th></tr>
			<?php foreach($records as $record):
				$class = ('alternate' == @$class) ? '' : 'alternate';?>
				<tr class="<?php echo $class?>">				
				<td><?php echo $record->id?></td>
				<td><?php echo empty($record->user_id) ? $record->ip : $record->user_nicename;
				if(!empty($record->email)) echo '<br>'.$record->email;?></td>
				<td><?php echo date_i18n($dateformat.' '.$timeformat, strtotime($record->datetime));
				if(!empty($record->source_url)): printf('<br>'.__('Source: %s', 'chained'), $record->source_url); endif; ?></td>
				<td><?php echo $record->points?></td><td><?php echo stripslashes($record->result_title);
				if(sizeof($record->details)):?><p><a href="#" onclick="jQuery('#recordDetails<?php echo $record->id?>').toggle();return false;"><?php _e('View details', 'chained');?></a></p><?php endif;?></td>
				<td><a href="#" onclick="chainedQuizDelete(<?php echo $record->id?>);return false;"><?php _e('Delete', 'chained')?></a></td></tr>
				
				<?php if(count($record->details)):?>
					<tr class="<?php echo $class?>" id="recordDetails<?php echo $record->id?>" style="display:none;">
						<td colspan="6">
							<table  width="100%"><tr><th><?php _e('Question', 'chained')?></th><th><?php _e('Answer', 'chained')?></th>
								<th><?php _e('Points', 'chained')?></th></tr>
							<?php foreach($record->details as $detail):?>
								<tr style="background:#EEE;"><td><?php echo stripslashes($detail->question)?></td><td><?php echo stripslashes($detail->answer_text);?></td>
									<td><?php echo $detail->points?></td></tr>
							<?php endforeach;?>	
							</table>						
						</td>	
					</tr>
				<?php endif;?>				
			<?php endforeach;?>
		</table>
		
		<p align="center"><?php if($offset > 0):?>
			<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&offset=<?php echo ($offset - 25)?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&<?php echo $filters_url;?>"><?php _e('previous page', 'chained')?></a>
		<?php endif;?> <?php if($count > ($offset + 25)):?>
			<a href="admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&offset=<?php echo ($offset + 25)?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&<?php echo $filters_url;?>"><?php _e('next page', 'chained')?></a> <?php endif;?></p>
			
			<form method="post">
				<p><input type="checkbox" onclick="this.checked ? jQuery('#chainedCleanupButton').show() : jQuery('#chainedCleanupButton').hide();"> <?php _e('Show button to cleanup all submitted data on this Algorithm.', 'chained')?></p>
				
				<div id="chainedCleanupButton" style="display:none;">
					<p style="color:red;"><b><?php _e('These operations cannot be undone!', 'chained')?></b></p>
					<p><input type="submit" name="cleanup_all" value="<?php _e('Cleanup all data', 'chained')?>"></p>				
				</div>
				<?php wp_nonce_field('chained_cleanup');?>
			</form>
			
			<h3>Did you know?</h3>
			<p>Future text.</p>
	<?php else:?>
		<p><?php _e('No one has taken this Algorithm yet.', 'chained')?></p>
		
	<?php endif;?>
	
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>
</div>

<script type="text/javascript" >
function chainedQuizDelete(id) {
	if(confirm("<?php _e('Are you sure?', 'chained')?>")) {
		window.location = 'admin.php?page=chainedquiz_list&quiz_id=<?php echo $quiz->id?>&offset=<?php echo $offset?>&ob=<?php echo $ob?>&dir=<?php echo $dir?>&del=' + id;
	}
}	
</script>