Moodle WebService for Collaborative Learning (Colle)
====================================================

The local plugin is designed to provide essential functionalities required by the Colle application within Moodle. It offers seamless integration and supports various functions, such as creating quizzes, retrieving context IDs from modules, and fetching quiz-related data through REST API calls. Moodle doesn't do these things by default, so this plugin fills in the gaps.

Some example calls to the webservice function:

* Create Quiz:
  https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_colle_create_quiz&courseid=4&quizname=QuizName

* Get All Quiz:
  https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_colle_get_all_quiz

* Get Quiz Created by Specific Teacher:
  https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_colle_get_quiz&userid=4

* Get Context ID from Module:
  https://yoursite.com/webservice/rest/server.php?wstoken=yOurT0k3n&moodlewsrestformat=json&wsfunction=local_colle_get_context_id&courseid=2&quizid=1


Configuration
-------------
No configuration needed, just install the plugin. Keep in mind to add the functions to the rest-service.

Usage
-----
Use functions over Rest API.

Requirements
------------
- Moodle 4.3 or later

Installation
------------
Copy the files into your /local/colle directory. Add the functions to your rest-service. 

Author
------
- Mohammad Fathul'ibad
- Fardan Al Jihad
- Annisa Dinda Gantini
