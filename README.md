# Triage Algorithm
    Contributors: innershell
    Tags: triage, form, forms, custom form, form builder, survey, survey builder, questionnaire, questionnaire builder, quiz, quiz builder, exam, exam builder, test, test builder, planner, planning, screening
    Requires at least: 5.0
    Tested up to: 5.1.1
    Requires PHP: 7.2
    Stable tag: trunk
    License: GPL2 or later

Create a triage algorithm that pre-screen a patient and generate a physician SOAP note for clinical decision support.

# Description
Design algorithms to screen and triage patients with chronic diseases at a medical facility. Questions are presented to the patient to gather information for their visit. The collected information are analyzed by the algorithm and presented to a clinician in the form of a medical assessment and/or treatment plan.

**To publish a triage algorithm, place its shortcode in a post or page.**

# Features
#### Create algorithms to ask questions
- An unlimited number of algorithms and questions are supported.

#### Present questions in multiple formats
- Generate user input forms by presenting single choice (radio buttons), multiple choice (checkboxes), single free text (textarea), or multiple free text (textarea) answers.

#### Decision tree modifies the algorithm in real-time
- Define how the algorithm reacts based on intput to the question. Different paths are selected based on input received.

#### Analyze answers for clinical decision support
- This is where the real magic happens. The algorithm processes answers for presentation to medical professionals in a clinical language and format. Provide the medical professional with an assessment of the patient and treatment plan for the patient.

#### Display dashboard of algorithm submissions
- Present all submissions in a summary dashboard for presenter to review and provide feedback.

#### Export submission data to a CSV file
- Extract raw data into an external file for a variety of offline uses.

# Installation
1. Upload the plugin zip file to WordPress in the 'Plugins' section.
..* Alternatively, unzip and upload the entire 'triage-algorithm' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin.
3. Go to "Triage Algorithm" settings to manage the plugin and setup algorithms.
4. Place the shortcode(s) in a post or page to publish the features.
5. View the post or page to see the triage algorithm in action.

**Attention Multi-Site (WP Network) Users!** 
- The plugin is perfectly compatible with multi-site installations but it should be activated as **blog admin** and NOT as superadmin.

# Upgrading
1. Backup your data.
2. Deactivate the plugin.
3. Delete the plugin (Note: Your data will NOT be deleted);
4. Follow the installation steps above to install the new plugin.
5. Make sure to activate the pluging.

# Getting Started
Once activated the plugin go to Triage Algorithm -> Algorithms in your WP dashboard and create your first algorithm. After entering the algorithm title, description and other settings you will be redirected to create the algorithm results / outcomes.

Creating results is optional but very powerful because you can present completely different content to the user depending on what path they took through the algorithm. You can use the result description box for this result-dependent content or even redirect to another page.

After you create your results you will be redirected to creating the actual questions in the algorithm. The answer to each question has an action which defines what happens if the user selects it: they can go to the next question, to a specific selected question (this is where the chaining magic happens), or to finalize the algorithm. 

Don't forget that the algorithm must be **published** before it becomes accessible. Publishing happens when you manually place the shortcode of the algorithm in a post or page or select the option "Automatically publish" when you save it.

# Frequently Asked Questions
None yet.

# Screenshots
1. Creating a triage algorithm question.
2. How the triage algorithm is displayed.
3. The SOAP note processed by the triage algorithm for clinical decision support.
4. The submissions dashboard to capture feedback for machine learning.

# Changelog
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
