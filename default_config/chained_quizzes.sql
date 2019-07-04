SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


TRUNCATE TABLE `wp_chained_quizzes`;
INSERT INTO `wp_chained_quizzes` (`id`, `title`, `output`, `email_admin`, `email_user`, `require_login`, `times_to_take`, `save_source_url`, `set_email_output`, `email_output`) VALUES
(18, 'Screening-General Anxiety Disorder', 'Congratulations, you completed the quiz!\r\n<h2>{{result-title}}</h2>\r\n{{result-text}}\r\n\r\nYou achieved {{points}} points from {{questions}} questions.', 1, 0, 0, 0, 0, 0, ''),
(17, 'Comorbidity-HTN', '{{soap-note}}', 1, 0, 0, 0, 0, 1, '{{soap-note}}'),
(3, 'Diarrhea', 'Thank you for completing the questionnaire.\r\n<h2>{{result-title}}</h2>\r\n{{result-text}}\r\n{{answers-table}}', 0, 0, 0, 0, 0, 0, ''),
(16, 'Survey-Patient Feedback', 'Thank you for your feedback.\r\n\r\n[redirect url=\\\'http://app.windwake.io/Diabetes\\\' sec=\\\'1\\\']', 0, 0, 1, 0, 0, 1, '{{answers-table}}'),
(9, 'Screening-Melvin\\\'s Regression Testing', '<strong>SOAP NOTE</strong>\r\n{{soap-note}}', 0, 0, 0, 0, 0, 1, '{{soap-note}}\r\n\r\n\r\n** This is system generated e-mail. Please do not reply to this message. **'),
(10, 'Routine Diabetes Encounter v2', 'Thank you for completing this form.\r\n<h2>{{result-title}}</h2>\r\n{{result-text}}\r\n{{answers-table}}', 0, 0, 0, 0, 0, 0, ''),
(11, 'Screening-Diabetes', 'Your answers have been accepted and sent to your doctor.\r\n\r\nWould you like to provide feedback about this diabetes triage system?   <a href=\\\"/diabetes/\\\">No</a>   |   <a href=\\\"/patient-feedback/\\\">Yes</a>\r\n\r\n&nbsp;\r\n\r\n&nbsp;\r\n\r\n[redirect url=\\\'http://app.windwake.io/Diabetes\\\' sec=\\\'10\\\']', 1, 1, 1, 0, 1, 1, '{{soap-note}}\r\n\r\n<strong>Eligible diabetes billing codes:</strong>\r\n99214; 99394-7; G2012;'),
(12, 'Weight Management', 'Thank you for completing this form.\r\n<h2>{{result-title}}</h2>\r\n{{result-text}}\r\n\r\n{{soap-note}}\r\n\r\n{{answers-table}}', 0, 0, 0, 0, 0, 0, ''),
(15, 'Screening-Taylor\\\'s Diabetes Demo', '{{soap-note}}', 0, 0, 0, 0, 1, 1, '{{soap-note}}\r\n\r\n** This is system generated e-mail. Please do not reply to this message. **'),
(19, 'Screening-Depression', '{{soap-note}}', 1, 0, 0, 0, 0, 1, '{{soap-note}}'),
(20, 'Screening-ETOH', '{{soap-note}}', 1, 0, 0, 0, 0, 1, '{{soap-note}}');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
