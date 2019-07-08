<p class="page-title">WindWake Orchestra User Manual</p>

<h1>Creating Orchestra Users</h1>
<p>A user account is required to perform a topic questionnaire so that Orchestra 
can track which user performed the topic. The user account will also be used 
to send the SOAP note to the performing user's e-mail address on file.</p>
<p>For patients without a user account, create a general user account for the 
clinic to login before allowing the patient to use Orchestra. This is so that 
Orchestra can administer the <i>Topic</i> questionnaire to the patient and send 
the final patient responses to the clinic's user account via fax or e-mail.</p>
<ol>
	<li>Login to Orchestra.</li>
	<li>Open the <i>User Settings</i> page</li>
	<li>Click the [Add New] button.</li>
	<li>Populate the required fields.</li>
	<li>Set the <i>Role</i> to "Subscriber" to give users access to perform Topics.</li>
</ol>
<p class="warning-text">WARNING: Pay attention to the Role you are giving the user. An 
“Administrator” Role will give the user <strong>FULL</strong> access to WindWake Orchestra. 
<strong>This should be avoided!</strong></p>
<br />


<h1>Configuring Orchestra</h1>
<h2>Topics</h2>
<p><i>Topics</i> are also known as either "<i>Instruments</i>" or "<i>Algorithms</i>" to be 
administered to a patient as a questionnaire. Each <i>Topic</i> contains a collection of 
<i>Questions</i> for the patient to answer and corresponding <i>Results</i> that that are 
determined based on the patient's response. Topics, Questions, and Results must all be 
configured by the Orchestra administrator.</p>
<ol>
	<li>Login to Orchestra.</li>
	<li>Click on <i>Orchestra</i> on the sidebar.</li>
	<li>Click on the <i>Configure Topics</i> submenu.</li>
	<li>Create a new Topic, Questions, and Results. A <i>Shortcode</i> will automatically be generated. 
		Remember this shortcode.</li>
	<li>Create a new Wordpress Page and insert the shortcode in the content area.</li>
	<li>View the Wordpress Page to see and perform the <i>Question</i>.</li>
</ol>


<h2>Questions</h2>
<p>These are the questions presented to the patient with corresponding answers configured 
by the Orchestra administrator. A variety of answers may be provided and each answer may 
also contain a point value used later to calculate the final score and result.</p>
</p>
<ol>
	<li>Create Questions under the selected Topic.</li>
	<li>Enter a <i>Provider Note</i> for all Answers. The SOAP note will only display content 
		if the <i>Provider Note</i> contains text.</li>
	<li>For <i>Text</i> answers, the text configured in the <i>Answer</i> field will be used 
		as the field label. If nothing is entered, the field itself will not be displayed to 
		the patient.</li>
	<li>Enter a <i>Patient Note</i> to create a personalized note for the patient. The note 
		presented will be based on the answer selected by the patient in the questionnaire.
</ol>

<h3>Multiple Choice (Checkbox) Answers</h3>
<p>If multiple (checkbox) answers have follow-up questions, you have to be very explicit about 
which question to display next for each choice.</p>
<ol>
	<li>Checkbox answers that do not need follow-up must directly specify the next question to ask.</li>
	<li>Checkbox answers with follow-up must also directly specify the next question to ask.</li>
	<li>Checkbox answers specified with "Next Question" will use go to the next question on the list in 
	sequential order. This means, if the next question in the list was a follow-up question to a different 
	checkbox answer, the topic will show this.
</ol>


<h2>Points & Results</h2>
<p>Results are based on points calculated by the answers chosen by the user. Each final 
score can have a different result for the completed questionnaire.</p>
<ol>
	<li>Create <i>Results</i> under the selected <i>Topic</i>.</li>
	<li>Enter a <i>Min. Points</i> and <i>Max. Points</i> for the result.</li>
	<li>Multiple results can be provided for each <i>Topic</i>. However, ensure that the points 
		do not overlap or the system will get "confused".</li>
</ol>

<h3>Aborting Questions</h3>
<p>The questionnaire can be aborted when the accumulated points fall within the abort range.</p>
<ul>
	<li>Enable abort by picking a question that will serve as the abort point of the questionnaire.</li>
	<li>Insert multiple abort points for a topic if more abort points are required.</li>
</ul>

<h2>Shortcodes</h2>
<p>To publish a topic, place its shortcode in a post or page. The following shortcodes are 
	currently offered by WindWake Orchestra:</p>
<ol>
	<li>[triage-topic XX] - Where XX refers to the topic ID.</li>
	<li>[triage-submissions topic="XX"] - Where XX refers to the topic ID.</li>
</ol>
<br />

<h1>Other Information</h1>
<h2>Cyber Security</h2>
<p>Your data is important to us. WindWake Orchestra employs industry-standard cyber 
	security measures for data privacy protection.</p>
<ol>
	<li><strong>SSL (128-bit) encryption</strong> is used to secure the transmission 
		of data with WindWake Orchestra. This is the same 	technology used by banks 
		to secure your data transmission for online banking. Look for the “https” or 
		“lock” icon in the browser 	address bar to confirm that your connection is 
		secured.</li>
	<li>A <strong>unique WindWake Orchestra login account</strong> is provided to 
		each client to access the <i>Topic</i>.</li>
	<li>An <strong>idle timeout setting</strong> automatically disconnects inactive 
		sessions after several days of non-use. Simply login again with the provided 
		username & password to secure your connection.</li>
	<li><strong>Automatic updates</strong> ensure that the latest software is always 
	used for WindWake Orchestra so that security vulnerabilities are immediately fixed.</li>
</ol>

<h2>Troubleshooting</h2>
<p><strong>“ALERT: PROVIDER_NOTE not setup for the following question/answer”</strong><br />
Ensure that the ‘Provider Note’ field has some text entered. Otherwise, it will display the ALERT and append the Question/Answer into the SOAP note.</p>

<p><strong>The ‘Provider Note’ text is not used for Text answers.</strong><br />
Ensure that ‘Answer’ field has some text. The topic uses the text in the ‘Answer’ field as the field label for the Topic when presented to the patient.</p>