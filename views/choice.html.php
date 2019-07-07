<p>
	<!-- Answer -->
	<div>
		<div class="one-line">
			<?php _e('Patient Answer:', 'chained')?><br>
			<textarea rows="1" cols="89" name="<?php echo empty($choice->id)?'answers[]':'answer'.$choice->id?>"><?php echo stripslashes(@$choice->choice)?></textarea>
		</div>
	</div>
	<div>
	<div class="one-line">
			<?php _e('Patient Note:', 'chained')?><br>
			<textarea rows="2" cols="40" name="<?php echo empty($choice->id)?'patient_notes[]':'patient_note'.$choice->id?>"><?php echo stripslashes(@$choice->patient_note)?></textarea>
		</div>
		<div class="one-line">
			<?php _e('Provider Subject/Objective Note:', 'chained')?><br>
			<textarea rows="2" cols="40" name="<?php echo empty($choice->id)?'provider_notes[]':'provider_note'.$choice->id?>"><?php echo stripslashes(@$choice->provider_note)?></textarea>
		</div>
	</div>
	<div>
		<div class="one-line">
			<?php _e('Assessment:', 'chained')?><br>
			<textarea rows="2" cols="40" name="<?php echo empty($choice->id)?'assessments[]':'assessment'.$choice->id?>"><?php echo stripslashes(@$choice->assessment)?></textarea>
		</div>
		<div class="one-line">
			<?php _e('Plan:', 'chained')?><br>
			<textarea rows="2" cols="40" name="<?php echo empty($choice->id)?'plans[]':'plan'.$choice->id?>"><?php echo stripslashes(@$choice->plan)?></textarea>
		</div>
	</div>	
	
	<!-- Points -->	
	<div class="one-line" style="width: 390px;">
		<?php _e('Answer Points:', 'chained')?><br>
		<input type="text" size="4" name="<?php echo empty($choice->id)?'points[]':'points'.$choice->id?>" value="<?php echo @$choice->points?>">
	</div>
	
	<!-- Correct Answer -->
	<!-- <input type="checkbox" name="<?php echo empty($choice->id)?'is_correct[]':'is_correct'.$choice->id?>" value="1" <?php if(!empty($choice->is_correct)) echo 'checked'?>>
	<?php _e('Correct answer','chained')?> | <?php _e('When selected go to:', 'chained')?> <select name="<?php echo empty($choice->id)?'goto[]':'goto'.$choice->id?>"> -->

	<!-- Next Question -->
	<div class="one-line">
		<?php _e('Next Question:', 'chained')?> <br> <select name="<?php echo empty($choice->id)?'goto[]':'goto'.$choice->id?>">
			<option value="next"><?php _e('Next question','chained')?></option>
			<option value="finalize" <?php if(!empty($choice->goto) and $choice->goto =='finalize') echo 'selected'?>><?php _e('Finalize quiz','chained')?></option>
			<?php if(sizeof($other_questions)):?>
				<option disabled><?php _e('- Select question -', 'chained')?></option>
				<?php foreach($other_questions as $other_question):?>
					<option value="<?php echo $other_question->id?>" <?php if(!empty($choice->id) and $choice->goto == $other_question->id) echo 'selected'?>><?php echo $other_question->title?></option>
				<?php endforeach;?>
			<?php endif;?>
		</select><br>
	</div>

	<!-- Delete Answer -->
	<?php if(!empty($choice->id)):?>
		<p><input type="checkbox" name="dels[]" value="<?php echo $choice->id?>"> <?php _e('Delete this choice', 'chained')?></p>
	<?php endif;?>
	<hr/>
</p>