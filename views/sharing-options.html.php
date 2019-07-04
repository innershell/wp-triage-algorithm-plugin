<div class="wrap">
	<h1><?php _e('Chained Quiz Social Sharing', 'chained')?></h1>
	<div class="postbox-container" style="width:73%;margin-right:2%;">
		<p><a href="admin.php?page=chained_quizzes"><?php _e('Back to quizzes','chained');?></a></p>
		
		<p><?php printf(__('You can use the shortcode %s to display Facebook share button on the "Final screen" on your quiz.', 'chained'), '<input type="text" value="[chained-share]" onclick="this.select();" readonly="readonly" size="20">');?></b></p>
		<p><?php printf(__('The social media buttons are provided by <a href="%s" target="_blank">Arbenting</a>. Feel free to replace them with other icons.', 'chained'), 'http://arbent.net/blog/social-media-circles-icon-set')?></p>
		
		<form method="post">
			<h2><?php _e('Facebook and LinkedIn Sharing', 'chained')?></h2>
			
			<p><label><?php _e('Your Facebook App ID:', 'chained')?></label> <input type="text" name="facebook_appid" value="<?php echo $appid?>"> <a href="https://developers.facebook.com/apps" target="_blank"><?php _e('Get one here', 'chained')?></a></p>
			<p><?php _e('If you leave it empty, no Facebook share button will be generated.', 'chained')?></p>
			
			<p><input type="checkbox" name="linkedin_enabled" value="1" <?php if($linkedin_options['enabled']) echo 'checked'?>> <?php _e('Show LinkedIn button', 'chained')?></p>
			
				<p><?php _e('Title:', 'chained')?> <input type="text" name="linkedin_title" value="<?php echo stripslashes(@$linkedin_options['title'])?>" size="40">
				<p><?php _e('Text:', 'chainedf')?> <textarea name="linkedin_msg" rows="4" cols="60"><?php echo stripslashes(@$linkedin_options['msg'])?></textarea>
				<br> <?php _e('You can use the variables {{{quiz-name}}}, {{{url}}}, {{{result-title}}} and {{{result-description}}}.', 'chained')?>
				<br>					
				<p><?php _e('If you leave title and text empty, result title and result description will be used respectively.', 'chained')?></p>	
				
				<p><b><?php _e('IMPORTANT: Facebook needs to be able to access your site to retrieve the social sharing data. If the site is on localhost or behind a htaccess login box sharing will not work properly.', 'chained')?></b></p>
				
				<h2><?php _e('Twitter Sharing Options:', 'chained')?></h2>	
			
			<p><?php _e('If you leave "Tweet text" empty the tweet text will be extracted from the result description. If it is empty, the result title will be used.', 'chained')?></p>
			
			<p><input type="checkbox" name="use_twitter" value="1" <?php if($twitter_options['use_twitter']) echo 'checked'?> onclick="jQuery('#twitterOptions').toggle();"> <?php _e('Show Tweet button', 'chained')?></p>
			
			<div id="twitterOptions" style="display:<?php echo empty($twitter_options['use_twitter']) ? 'none' : 'block'?>">
				<p><input type="checkbox" name="show_count" value="1" <?php if(!empty($twitter_options['show_count'])) echo 'checked'?>> <?php _e('Show count', 'chained')?></p>
				<p><?php _e('Via @', 'chained')?> <input type="text" name="via" value="<?php echo @$twitter_options['via']?>"></p>
				<p><?php _e('Hashtag #', 'chained')?><input type="text" name="hashtag" value="<?php echo @$twitter_options['hashtag']?>"></p>
				<p><?php _e('Tweet text (No more than 140 chars):', 'chained')?> <textarea name="tweet" maxlength="140" rows="3" cols="40"><?php echo stripslashes(@$twitter_options['tweet'])?></textarea>
				<br> <?php _e('You can use the variables {{{quiz-name}}}, {{{result-title}}} and {{{result-description}}}.', 'chained')?></p>			
			</div>	
			
			<p><input type="submit" value="<?php _e('Save All Settings', 'chained')?>"></p>
			<input type="hidden" name="ok" value="1">
			<?php wp_nonce_field('chained_social_sharing');?>
		</form>
	</div>
	<div id="chained-sidebar">
			<?php include(CHAINED_PATH."/views/sidebar.html.php");?>
	</div>	
</div>