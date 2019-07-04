<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class ChainedSharing {
	static function options() {
		global $wpdb;
		if(!empty($_POST['ok']) and check_admin_referer('chained_social_sharing')) {
			$_POST['facebook_appid'] = sanitize_text_field($_POST['facebook_appid']);
			if(!current_user_can('unfiltered_html')) {
				$_POST['linkedin_msg'] = strip_tags($_POST['linkedin_msg']);
			}
			
			$linkedin_enabled = empty($_POST['linkedin_enabled']) ? 0 : 1;
			update_option('chained_facebook_appid', $_POST['facebook_appid']);
			$linkedin_options = array("enabled" => $linkedin_enabled,  "msg"=>$_POST['linkedin_msg'], 'title' => $_POST['linkedin_title']);
			update_option('chained_linkedin', $linkedin_options);	
			$twitter_options = array("use_twitter" => intval(@$_POST['use_twitter']), 
			"show_count" => intval(@$_POST['show_count']),
			 "via"=>sanitize_text_field($_POST['via']), 
			 "hashtag" => sanitize_text_field($_POST['hashtag']), 
			 'large_button' => intval(@$_POST['large_button']),
			 "tweet"=>strip_tags($_POST['tweet']));
			update_option('chained_twitter', $twitter_options);
		}
		
		$appid = get_option('chained_facebook_appid');	
		$linkedin_options = get_option('chained_linkedin');
		$twitter_options = get_option('chained_twitter');
		include(CHAINED_PATH.'/views/sharing-options.html.php');
	}	
	
	// display the social sharing buttons
	static function display() {
		global $wpdb;
		$taking_id = intval($GLOBALS['chained_completion_id']);
		
		ob_start();
		// https://developers.facebook.com/docs/sharing/reference/feed-dialog
		$appid = get_option('chained_facebook_appid');
		
		// get the grade title and description
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_COMPLETED." WHERE id=%d", $taking_id));
		$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." WHERE id=%d", intval($GLOBALS['chained_result_id'])));
		
		// select quiz name
		$quiz_name = $wpdb->get_var($wpdb->prepare("SELECT title FROM ".CHAINED_QUIZZES." WHERE id = %d", @$taking->quiz_id));
			
		// keep linkedin vars always because they are also used in Facebook
		$linkedin = get_option('chained_linkedin');
		$linkedin_msg = stripslashes($linkedin['msg']);
		$linkedin_title = stripslashes($linkedin['title']);		
				
		// title and description set up?
		if(!empty($linkedin_title)) {
			$linkedin_title = str_replace('{{{result-title}}}', stripslashes(@$result->title), $linkedin_title);				
			$linkedin_title = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $linkedin_title);
		}
		if(!empty($linkedin_msg)) {
			$linkedin_msg = str_replace('{{{result-title}}}', stripslashes(@$result->title), $linkedin_msg);			
			$linkedin_msg = str_replace('{{{result-description}}}', stripslashes(@$result->description), $linkedin_msg);	
			$linkedin_msg = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $linkedin_msg);
			$linkedin_msg = str_replace('{{{url}}}', get_permalink($_POST['post_id']), $linkedin_msg);
		}
		
		// if not, default to grade title and desc
		if(empty($linkedin_title)) $linkedin_title = @$result->title;
		if(empty($linkedin_msg)) $linkedin_msg = @$result->description;
		
		$linkedin_title = stripslashes($linkedin_title);
		$linkedin_msg = stripslashes($linkedin_msg);	
		
		// any picture?
		$picture_str = '';
		if(strstr(@$result->gescription, '<img')) {
			// find all pictures in the grade descrption
			$html = stripslashes($result->gdescription);
			$dom = new DOMDocument;
			$dom->loadHTML($html);
			$images = array();
			foreach ($dom->getElementsByTagName('img') as $image) {
			    $src =  $image->getAttribute('src');	
			    $class = $image->getAttribute('class');
			    $images[] = array('src'=>$src, 'class'=>$class);
			} // end foreach DOM element
			
			if(sizeof($images)) {
				$target_image = $images[0]['src'];
				
				// but check if we have any that are marked with the class
				foreach($images as $image) {
					if(strstr($image['class'], 'chained-share')) {
						$target_image = $image['src'];
						break;
					}
				}
				
				$picture_str = "&picture=".urlencode($target_image);
			}
		}   // end searching for image
		
		$twitter_options = get_option('chained_twitter');
		
		// prepare tweet text
		if(!empty($twitter_options['use_twitter'])) {
			$tweet = stripslashes($twitter_options['tweet']);
			
			if(empty($tweet)) {
				$tweet = stripslashes($grade->gdescription);
				if(empty($tweet)) $tweet = stripslashes($grade->gtitle);
			}
			else {
				$tweet = str_replace('{{{result-title}}}', stripslashes(@$result->title), $tweet);
				$tweet = str_replace('{{{result-description}}}', stripslashes(@$result->description), $tweet);
				$tweet = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $tweet);								
			}
			
			$tweet = substr($tweet, 0, 140);
		}
		$shareable_url = site_url('?chained_sssnippet=1&amp;tid='.$taking_id.'&amp;creturn_to='.$_POST['post_id']);
		?>	
		<div><?php if(!empty($appid)):?><a title="Share your results on Facebook" onclick="return !window.open(this.href, 'Facebook', 'width=640,height=300')" href="https://www.facebook.com/dialog/feed?app_id=<?php echo $appid?>&amp;display=popup&amp;link=<?php echo urlencode(get_permalink($_POST['post_id']))?>&amp;name=<?php echo urlencode($linkedin_title)?>&amp;redirect_uri=<?php echo urlencode(get_permalink($_POST['post_id']))?>&amp;description=<?php echo urlencode($linkedin_msg)?><?php echo $picture_str?>" target="_blank"><img src="<?php echo CHAINED_URL.'img/share/facebook.png'?>"></a>&nbsp;
		<?php endif; // end if Facebook 
		if(!empty($linkedin['enabled'])):?>
	   	<script src="//platform.linkedin.com/in.js" type="text/javascript">
 			 lang: en_US
			</script>
		<script type="IN/Share" data-url="<?php echo $shareable_url;?>"></script>	 
	   <?php endif; // endif linkedin
		 if(!empty($twitter_options['use_twitter'])):?>
		 <a href="https://twitter.com/share" class="twitter-share-button watu-twitter-share-button" data-url="<?php echo get_permalink($_POST['post_id'])?>" data-via="<?php echo $twitter_options['via']?>" data-hashtags="<?php echo $twitter_options['hashtag']?>" data-text="<?php echo htmlentities($tweet)?>" <?php if(empty($twitter_options['show_count'])):?>data-count="none"<?php endif;?>>Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
	   <?php endif;?></div>
		<?php 
		$content = ob_get_clean();
		return $content;
	}
	
	// display snippets for social sharing
	// used to force G+ and LinkedIn to use proper content
	static function social_share_snippet() {
		global $post, $wpdb;
		
		if(empty($_GET['tid']) or empty($_GET['chained_sssnippet'])) return false;
		$taking_id = intval($_GET['tid']);
			
		// select taking
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_COMPLETED." WHERE id=%d", $taking_id));
		$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." WHERE id=%d", $taking->result_id));
		
		// select exam and make sure social sharing buttons are there. If not,  redirect to the post
		$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_QUIZZES." WHERE id=%d", $taking->quiz_id));
		
		if(!strstr($quiz->output, '[chained-share')) chained_redirect(get_permalink($_GET['creturn_to']));
		$quiz_name = $quiz->title;
				
		// select grade
		$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".CHAINED_RESULTS." WHERE id=%d", $taking->result_id));
		if(empty($result->title)) $result = (object)array("title"=>'None', 'description'=>'None');	
		
		// try to get the image
		// this code repeats in the social-sharing.php controller, let's try to avoid this
		$target_image = '';
		// any picture?
		$picture_str = '';
		if(strstr(@$result->gescription, '<img')) {
			// find all pictures in the grade descrption
			$html = stripslashes($result->gdescription);
			$dom = new DOMDocument;
			$dom->loadHTML($html);
			$images = array();
			foreach ($dom->getElementsByTagName('img') as $image) {
			    $src =  $image->getAttribute('src');	
			    $class = $image->getAttribute('class');
			    $images[] = array('src'=>$src, 'class'=>$class);
			} // end foreach DOM element
			
			if(sizeof($images)) {
				$target_image = $images[0]['src'];
				
				// but check if we have any that are marked with the class
				foreach($images as $image) {
					if(strstr($image['class'], 'chained-share')) {
						$target_image = $image['src'];
						break;
					}
				}
				
				$picture_str = "&picture=".urlencode($target_image);
			}
		}   // end searching for image
		
	 	// prepare open graph title & description - same for LinkedIn and Gplus 
		$linkedin = get_option('chained_linkedin');
		$og_msg = stripslashes($linkedin['msg']);
		$og_title = stripslashes($linkedin['title']);
				
		// title and description set up?
		if(!empty($og_title)) {
			$og_title = str_replace('{{{result-title}}}', stripslashes($result->title), $og_title);				
			$og_title = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $og_title);
		}
		if(!empty($og_msg)) {
			$og_msg = str_replace('{{{result-title}}}', stripslashes($result->title), $og_msg);			
			$og_msg = str_replace('{{{result-description}}}', stripslashes($result->description), $og_msg);	
			$og_msg = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $og_msg);
			$og_msg = str_replace('{{{url}}}', $_GET['creturn_to'], $og_msg);
		}
		
		// if not, default to grade title and desc
		if(empty($og_title)) $og_title = $result->title;
		if(empty($og_msg)) $og_msg = $result->description;
		
		$og_title = stripslashes($og_title);
		$og_msg = stripslashes($og_msg);	
		
		$og_description = str_replace('"',"'",$og_msg);
		$og_description = str_replace(array("\n","\r")," ",$og_description);	
		$og_description = strip_tags($og_description);
		$og_title = str_replace('"',"'",$og_title);
		$og_title = str_replace(array("\n","\r")," ",$og_title);
		
		include(CHAINED_PATH."/views/social-share-snippet.html.php");
		exit;
	}
}