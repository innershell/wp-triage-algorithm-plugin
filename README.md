# Triage Algorithm
    Contributors: Melvin Tan, Taylor Martin
    Tags: triage, algorithm, decision support, quiz, exam, test, questionnaire, survey
    Requires at least: 3.3
    Tested up to: 5.0
    Stable tag: trunk
    License: GPL2

Create a triage algorithm that pre-screen a patient and generate a physician SOAP note for clinical decision support.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

# Description
This is an unique conditional logic plugin that lets you create set a questions whose answers generate a physician SOAP note.

**To publish a triage algorithm, place its shortcode in a post or page.**

# Features
#### Create unlimited number of triage algorithms
This plugin has no limitations on the number of triage algorithms you can have.

#### Question types support: single-choice, multiple-choice, text
The plugin will generate respectively a group of radio buttons, checkboxes, or a text area. 

#### Define what to do when specific answer is chosen
You can define to go to next question, go to a specific selected question, or finish the algorithm. 

#### Export user's answers to a CSV file - with or without details
The CSV file can be used to analyze user results in Excel, import it in a database and so on.

# Getting Started
Once activated the plugin go to Triage Algorithm -> Algorithms in your WP dashboard and create your first algorithm. After entering the algorithm title, description and other settings you will be redirected to create the algorithm results / outcomes.

Creating results is optional but very powerful because you can present completely different content to the user depending on what path they took through the algorithm. You can use the result description box for this result-dependent content or even redirect to another page.

After you create your results you will be redirected to creating the actual questions in the algorithm. The answer to each question has an action which defines what happens if the user selects it: they can go to the next question, to a specific selected question (this is where the chaining magic happens), or to finalize the algorithm. 

Don't forget that the algorithm must be **published** before it becomes accessible. Publishing happens when you manually place the shortcode of the algorithm in a post or page or select the option "Automatically publish" when you save it.

** Attention Multi-Site (WP Network) Users! **
The plugin is perfectly compatible with multi-site installations but it should be activated as **blog admin** and NOT as superadmin.

# Installation
1. Unzip the contents and upload the entire `triage-algorithm` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Triage Algorithm" in your menu and manage the plugin
4. To publish an algorithm place its shortcode in a post or page

# Frequently Asked Questions
None yet.

# Screenshots
1. The create / edit algorithm form lets you give a title and specify the dynamic end output
2. Here is how the different choices can be connected to different outcomes (plus assigning points at the same time)
3. And of course you can define different results depending on the results collected in the quiz 

# Changelog
## Version 1.0
- First public release of a triage algorithm
