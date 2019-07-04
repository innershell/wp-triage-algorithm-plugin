=== Triage Algorithm ===
Contributors: innershell
Tags: triage, form, forms, custom form, form builder, survey, survey builder, questionnaire, questionnaire builder, quiz, quiz builder, exam, exam builder, test, test builder, planner, planning, screening
Requires at least: 5.0
Tested up to: 5.1.1
Requires PHP: 7.2
Stable tag: trunk
License: GPL2 or later

Create algorithms that gathers information from patients for clinical decision support.

== Description ==

Design algorithms to screen and triage patients with chronic diseases at a medical facility. Questions are presented to the patient to gather information for their visit. The collected information are analyzed by the algorithm and presented to a clinician in the form of a medical assessment and/or treatment plan.

###Features###
= Create algorithms to ask questions. =
An unlimited number of algorithms and questions are supported.

= Present questions in multiple formats. =
Generate user input forms by presenting single choice (radio buttons), multiple choice (checkboxes), single free text (textarea), or multiple free text (textarea) answers.

= Decision tree modifies the algorithm in real-time. =
Define how the algorithm reacts based on intput to the question. Different paths are selected based on input received.

= Analyze answers for clinical decision support. =
This is where the real magic happens. The algorithm processes answers for presentation to medical professionals in a clinical language and format. Provide the medical professional with an assessment of the patient and treatment plan for the patient.

= Display dashboard of algorithm submissions. =
Present all submissions in a summary dashboard for presenter to review and provide feedback.

= Export submission data to a CSV file =
Extract raw data into an external file for a variety of offline uses.

== Installation ==

1. Upload the plugin zip file to WordPress in the 'Plugins' section.
* Alternatively, unzip and upload the entire 'triage-algorithm' folder to the '/wp-content/plugins/' directory.
1. Activate the plugin.
1. Go to "Triage Algorithm" settings to manage the plugin and setup algorithms.
1. Place the shortcode(s) in a post or page to publish the features.
1. View the post or page to see the triage algorithm in action.

*** Attention Multi-Site (WP Network) Users! ***
The plugin is perfectly compatible with multi-site installations but it should be activated as **blog admin** and NOT as superadmin.

= Upgrading =
1. Backup your data.
1. Deactivate the plugin.
1. Delete the plugin (Note: Your data will NOT be deleted);
1. Follow the installation steps above to install the new plugin.
1. Make sure to activate the plugin.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Creating a triage algorithm question.
1. How the triage algorithm is displayed.
1. The SOAP note processed by the triage algorithm for clinical decision support.
1. The submissions dashboard to capture feedback for machine learning.

== Changelog ==

= 2.0.2 (Bug Fix) =
- Emails to Admin when submissions received not working.
- Text fields not required allowing patients to submit blank answers.
- Fixed plugin activation error.
- Fixed plugin cannot be upgraded by deleting/uploading new ZIP file.

= 2.0.1 (Bug Fix) =
- Submissions dashboard had no clickable link to open SOAP note when user did not provide a Study ID.
- SOAP note not saved properly. Submissions dashboard did not display SOAP note.
- Submissions dashboard cannot filter results for specific triage algorithms.
- Submissions (WordPress admin mode) did not display text answers. Displayed answer ID instead.

= 2.0 (Dashboard) =
- Dashboard to review triage algorithm submissions.
- Use shortcodes to publish the submissions dashboard.

= 1.0 (Triage Algorithm) =
- Create triage algorithms with unlimited questions and answers.
- Use shortcodes to publish algorithms.
- Present algorithm questionnaires and save results.
- Generate SOAP note for clinical decision support.
- E-mail or fax SOAP notes to an external destination.
