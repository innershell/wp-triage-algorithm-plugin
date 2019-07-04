<div class="wrap">
	<table class="widefat">
		<th>Response Date</th>
		<th>Algorithm Name</th>
		<th>Study ID</th>
		<?php foreach ($results as $result):
			$class = ('alternate' == @$class) ? '' : 'alternate';?>
			<tr class="<?php echo $class?>">
				<td><?php echo $result->response_date?></td>
				<td><?php echo $result->algorithm_name?></td>
				<td><a href="#" onclick="jQuery('#soapNote<?php echo $result->completion_id?>').toggle();return false;"><?php echo $result->study_id?></a></td>
			</tr>
			<tr>
				<td id="soapNote<?php echo $result->completion_id?>" colspan="3" cellpadding="30" style="display: none;">
					<div style="padding: 15px 50px 15px 50px;">
					<div class="soap-note">
						<div style="text-align: right;"><label>Options:</label> <a href="#" onclick="chainedQuiz.disagree(<?php echo $result->completion_id?>, '<?php echo admin_url('admin-ajax.php') ?>');">Submit Feedback</a></div>
						<?php echo $result->soap_note?>
					</div>
					</div>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
