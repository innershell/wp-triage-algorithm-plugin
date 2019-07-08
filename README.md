# Triage Algorithm
    Contributors: innershell
    Tags: triage, form, forms, custom form, form builder, questionnaire, questionnaire builder, screening
    Requires at least: 5.0
    Tested up to: 5.2.2
    Requires PHP: 7.2
    Stable tag: trunk
    License: GPL2 or later

Create a triage algorithm that pre-screens a patient and generate a physician SOAP note for clinical decision support.

# Description
Design topics to screen and triage patients with chronic diseases. A series of questions for a topic is presented to the patient to gather information for their medical condition. The collected information is analyzed by the system and presented to a clinician in the form of a medical assessment and/or treatment plan.

# Features
#### Create topics to ask a series of questions.
- An unlimited number of topics and questions are supported.

#### Present questions in multiple formats.
- Generate user input forms by presenting single choice (radio buttons), multiple choice (checkboxes), free text (textarea), and date answers.

#### Decision tree modifies the questionnaire in real-time
- Define how the system reacts based on answers selected by the user. Different paths are selected based on user input.

#### Analyze answers for clinical decision support
- This is where the real magic happens. The system processes answers for presentation to medical professionals in a clinical language and format. Provides the medical professional with an assessment of the patient's condition and personalized treatment plan for the patient.

#### Points-based subsystem for scoring a questionnaire.
- Assign points to answers and allow the system to calculate the final score of the questionnaire.

#### Display dashboard of user submissions
- Present all submissions in a summary dashboard for providers to review and provide feedback.

#### Export submission data to a CSV file
- Extract raw data into an external file for a variety of offline uses.

# Installation
1. Upload the plugin zip file to WordPress in the 'Plugins' section.
..* Alternatively, unzip and upload the entire 'triage-algorithm' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin.
3. Go to "Triage Algorithm" settings to manage the plugin and setup algorithms.
4. Place the shortcode(s) in a post or page to publish the features.
5. View the post or page to see the system in action.

**Attention Multi-Site (WP Network) Users!** 
- The plugin is perfectly compatible with multi-site installations but it should be activated as **blog admin** and NOT as superadmin.

# Upgrading
1. Backup your data.
2. Deactivate the plugin.
3. Delete the plugin (Note: Your data will **NOT** be deleted **UNLESS** you set it in the options.);
4. Follow the installation steps above to install the new plugin.
5. Make sure to activate the plugin.

# Getting Started
Navigate to `Orchestra -> Configure Topics` in your WordPress dashboard and create your first topic. After giving the topic a title, description and other settings, you will be redirected to create the topic results / outcomes.

Creating results is optional, but very powerful, because you can present completely different content to the user depending on what path they took through the questionnaire. You can use the result description box for this result-dependent content or even redirect to another page.

After you create your results you will be redirected to creating the actual questions in the algorithm. The answer to each question has an action which defines what happens if the user selects it: 
- Go to the next question
- Go to a specific selected question (this is where the chaining magic happens)
- Finalize the algorithm. 

Don't forget that the algorithm must be **published** before it becomes accessible. Publishing happens when you manually place the shortcode of the topic in a post or page or select the option "Automatically publish" when you save it.

# Frequently Asked Questions
See the online help in your WordPress dashboard.

# Changelog
## 6.0 (Abort Algorithm)
- Renamed 'Algorithms' to 'Topics' as algorithms sounded more technical and complex than necessary.
- Questions can prematurely *abort* a topic when accumulated points are within the abort range. Multiple abort points per topic can be setup.
- New *Patient Note* field to setup personalized notes for a patient for each answer selected.
- New `{{patient-note}}` injection code to insert patient notes in the patient output or provider output.
- Ability to delete all plugin data when uninstalling the plugin.  
- User interface improvements.
- Fixed various backend errors that did not affect frontend user functionality.

## 5.1 (Show Answers on Questions Page)
- Show/hide answers in the questions listing page (admin only).
- Hyperlink from the answer to the next question to ask.
- Fixed bug where published algorithms are not being hyperlinked.

## 5.0 (Checkbox Improvements)
- Scores can have different SOAP note sections.
- Added Date questions that display a calendar date picker.
- Follow-up questions to multiple checkbox answers selected.

## 4.0 (Points)
- Algorithms can generate a score (i.e., points).
- Sort algorithms listing by title.
- Debug mode to turn on extra helpful text to find algorithm setup problems.

## 3.0 (Algorithm Billing Codes)
- Each algorithm can be given a billing code by free text to the email output portion of the algorithm setup.

## 2.0.2 (Bug Fix)
- Emails to Admin when submissions received not working.
- Text fields not required allowing patients to submit blank answers.
- Fixed plugin activation error.
- Fixed plugin cannot be upgraded by deleting/uploading new ZIP file.

## 2.0.1 (Bug Fix)
- Submissions dashboard had no clickable link to open SOAP note when user did not provide a Study ID.
- SOAP note not saved properly. Submissions dashboard did not display SOAP note.
- Submissions dashboard cannot filter results for specific triage algorithms.
- Submissions (WordPress admin mode) did not display text answers. Displayed answer ID instead.

## 2.0 (Dashboard)
- Dashboard to review triage algorithm submissions.
- Use shortcodes to publish the submissions dashboard.

## 1.0 (Triage Algorithm)
- Create triage algorithms with unlimited questions and answers.
- Use shortcodes to publish algorithms.
- Present algorithm questionnaires and save results.
- Generate SOAP note for clinical decision support.
- E-mail or fax SOAP notes to an external destination.
