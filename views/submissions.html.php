<div class="wrap">
	<table class="widefat">
		<th><?php _e('Response Date', 'chained')?></th>
		<th><?php _e('Topic Name', 'chained')?></th>
		<th><?php _e('Study ID', 'chained')?></th>
		<?php foreach ($results as $result):
			$class = ('alternate' == @$class) ? '' : 'alternate';?>
			<tr class="<?php echo $class?>">
				<td><?php echo $result->response_date?></td>
				<td><?php echo $result->topic_name?></td>
				<td><a href="#" onclick="jQuery('#soapNote<?php echo $result->completion_id?>').toggle();return false;"><?php echo $result->study_id==null?'NOT PROVIDED':$result->study_id?></a></td>
			</tr>
			<tr>
				<td id="soapNote<?php echo $result->completion_id?>" colspan="3" cellpadding="30" class="soap-note-canvas" style="display: none;">
					<div style="padding: 20px 50px 20px 50px;">
						<div class="soap-note">
							<div style="text-align: right;">
								X [<a href="#" onclick="jQuery('#soapNote<?php echo $result->completion_id?>').toggle();return false;">Close</a>]
							</div>
							<p></p>
							<div style="text-align: center;">
								<label>DISAGREE with this SOAP note?</label><BR>
								<a href="#" onclick="chainedQuiz.disagree(<?php echo $result->completion_id?>, '<?php echo admin_url('admin-ajax.php') ?>');">Submit Feedback (Click Here)</a>
							</div>
							<?php echo $result->soap_note?>
						</div>
					</div>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
