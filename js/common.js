chainedQuiz = {};
chainedQuiz.points = 0; // initialize the points as 0
chainedQuiz.questions_answered = 0;

chainedQuiz.goon = function(quizID, url) {
	// make sure there is answer selected
	var qType = jQuery('#chained-quiz-form-' + quizID + ' input[name=question_type]').val();	
	var chkClass = 'chained-quiz-' + qType;
	jQuery('#chained-quiz-action-' + quizID).attr('disabled', true);
	
	// is any checked?
	var anyChecked = false;
	jQuery('#chained-quiz-form-' + quizID + ' .' + chkClass).each(function(){
		if(this.checked) anyChecked = true; 	
	});
	
	if(!anyChecked && qType != 'text') {
		alert(chained_i18n.please_answer);
		jQuery('#chained-quiz-action-' + quizID).removeAttr('disabled');
		return false;
	}

	// is textarea filled?
	if((qType == 'text') && jQuery('#chained-quiz-form-' + quizID + ' textarea[name=answer_text]').val() == '') {
		alert(chained_i18n.please_answer);
		jQuery('#chained-quiz-action-' + quizID).removeAttr('disabled');
		return false;
	}
	
	// submit the answer by ajax
	data = jQuery('#chained-quiz-form-'+quizID).serialize();
	data += '&action=chainedquiz_ajax';
	data += '&chainedquiz_action=answer';
	this.questions_answered++;
	data += '&total_questions=' + this.questions_answered;
	
	// console.log(data);
	jQuery.post(url, data, function(msg) {
		  parts = msg.split("|CHAINEDQUIZ|");
		  points = parseFloat(parts[0]);
		  if(isNaN(points)) points = 0;
		  chainedQuiz.points += points;		  
			
			if(jQuery('body').scrollTop() > 250) {				
				jQuery('html, body').animate({
			   		scrollTop: jQuery('#chained-quiz-wrap-'+quizID).offset().top -100
			   }, 500);   
			}		  
			
		  jQuery('#chained-quiz-action-' + quizID).removeAttr('disabled');
		  
		  // redirect?
		  if(parts[1].indexOf('[CHAINED_REDIRECT]') != -1) {
		  	  var sparts = parts[1].split('[CHAINED_REDIRECT]');
		  	  window.location=sparts[1];
		  }
		  else {
		  		// load next question or the final screen
		  	  jQuery('#chained-quiz-div-'+quizID).html(parts[1]);	
		  	  
			  // hide/show "go ahead" button depending on comment in the HTML code
			  if(parts[1].indexOf('<!--hide_go_ahead-->') != -1) jQuery('#chained-quiz-action-' + quizID).hide();
			  else jQuery('#chained-quiz-action-' + quizID).show();			  	  
		  	  	  
		     jQuery('#chained-quiz-form-' + quizID + ' input[name=points]').val(chainedQuiz.points);
		     chainedQuiz.initializeQuestion(quizID);
		  }
	});
}

chainedQuiz.initializeQuestion = function(quizID) {
	jQuery(".chained-quiz-frontend").click(function() {		
		if(this.type == 'radio' || this.type == 'checkbox') {		
			// enable button			
			jQuery('#chained-quiz-action-' + quizID).removeAttr('disabled');
		}
	});
	
	jQuery(".chained-quiz-frontend").keyup(function() {
		if(this.type == 'textarea') {
			jQuery('#chained-quiz-action-' + quizID).removeAttr('disabled');
		}
	});
}