<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service library functions
 *
 * @package    local_colle
 * @copyright  2020 corvus albus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/moodlelib.php');

/**
 * Web service API definition.
 *
 * @package local_colle
 * @copyright 2020 corvus albus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_colle_external extends external_api {

    // Functionset for get_roles() ************************************************************************************************.

    /**
     * Parameter description for get_roles().
     *
     * @return external_function_parameters.
     */
    public static function create_quiz_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'quizname' => new external_value(PARAM_TEXT, 'quiz name'),
            )
        );
    }

    /**
     * Return roleinformation.
     *
     * This function returns roleid, rolename and roleshortname for all roles or for given roles.
     *
     * @param array $courseid List of roleids.
     * @param array $quizname List of role shortnames.
     * @return array Array of arrays with role informations.
     */
    public static function create_quiz($courseid, $quizname) {
        global $DB;
        $params = self::validate_parameters(self::create_quiz_parameters(), array('courseid' => $courseid,
                    'quizname' => $quizname,));

        $q_param = array('courseid' => $params['courseid'],
            'quizname' => $params['quizname']);
        //var_dump("Query parameter".$q_param);
        $sql = "SELECT q.id
           FROM {quiz} q          
           WHERE q.course=:courseid AND q.name=:quizname";
        $rqa = $DB->get_record_sql($sql, $q_param);
        if (isset($rqa) && $rqa != null) {
            $result = array();
            $result['quizid'] = 0;
            $result['status'] = "failure";
            $result['message'] = "This quiz name already exists in this course. Please try with different quiz name";
        } else {
            $quiz = new stdClass();
            $quiz->course = $courseid;
            $quiz->name = $quizname;
            $quiz->intro = "Ini adalah quiz yang dibuat melalui API";
            $quiz->timecreated = time();
            $quiz->overduehandling = "autosubmit";
	    $quiz->preferredbehaviour = "deferredfeedback";
            $rqa = $DB->insert_record('quiz', $quiz);
            if (isset($rqa)) {
                $result = array();
                $result['quizid'] = $rqa;
                $result['status'] = "success";
                $result['message'] = "Quiz created succesfully";
            } else {
                $result = array();
                $result['quizid'] = 0;
                $result['status'] = "failure";
                $result['message'] = "Some error occured please try again";
            }
        }

        // Add module to course
        $sectionnum = 0;
        $instanceid = $rqa;
        course_create_sections_if_missing($courseid, $sectionnum);
        $moduleid = $DB->get_field('modules', 'id', array('name' => 'quiz'), MUST_EXIST);
        $sectionid = $DB->get_field('course_sections', 'id', array('course' => $courseid, 'section' => $sectionnum), MUST_EXIST);

        $newcm = new stdClass();
        $newcm->course           = $courseid;
        $newcm->module           = $moduleid;
        $newcm->section          = $sectionid;
        $newcm->added            = time();
        $newcm->instance         = $instanceid;
        $newcm->visible          = 1;
        $newcm->groupmode        = 0;
        $newcm->groupingid       = 0;
        $newcm->groupmembersonly = 0;
        $newcm->showdescription  = 0;
        $cmid = add_course_module($newcm);

        course_add_cm_to_section($courseid, $cmid, $sectionnum);

        $qs = new stdClass();
        $qs->quizid = $instanceid;
        $qs->firstslot = 1;
        $DB->insert_record('quiz_sections', $qs);
        

        return $result;
    }

    /**
     * Parameter description for create_sections().
     *
     * @return external_description
     */
    public static function create_quiz_returns() {
        return new external_single_structure(
                        array('quizid' => new external_value(PARAM_INT, 'id of quiz'),
                            'status' => new external_value(PARAM_ALPHA, "Status of quiz"),
                            'message' => new external_value(PARAM_TEXT, "quiz message")
                ));
    }

    /**
     * Parameter description for get_roles().
     *
     * @return external_function_parameters.
     */
    public static function get_context_id_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'quizid' => new external_value(PARAM_INT, 'quiz id'),
            )
        );
    }

    public static function get_context_id($courseid, $quizid) {
        global $DB;
        
        $params = self::validate_parameters(self::get_context_id_parameters(), array('courseid' => $courseid,
                    'quizid' => $quizid,));

        $cm = get_coursemodule_from_instance('quiz', $quizid, $courseid);
        $context = \context_module::instance($cm->id)->id;
        $result = array();
        $result['contextid'] = $context;

        return $result;
    }

    /**
     * Parameter description for create_sections().
     *
     * @return external_description
     */
    public static function get_context_id_returns() {
        return new external_single_structure(
                        array('contextid' => new external_value(PARAM_INT, 'id of quiz'),
                )
            );
    }

    /**
     * Parameter description for get_roles().
     *
     * @return external_function_parameters.
     */
    public static function get_quiz_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'user id'),
            )
        );
    }

    public static function get_quiz($userid) {
        global $DB;
        
        $params = self::validate_parameters(self::get_quiz_parameters(), array('userid' => $userid));
        
        $url = 'http://colle.southeastasia.cloudapp.azure.com/moodle/webservice/rest/server.php?wstoken=1f95ee6650d2e1a6aa6e152f6bf4702c&wsfunction=core_course_get_contents&moodlewsrestformat=json&courseid=2';
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $modules = $data[0]['modules'];

        $course_modules = array();
        foreach ($modules as $module) {
            if($module['modname'] == 'quiz'){
                $course_modules[] = array(
                    'id' => $module['id'],
                    'contextid' => $module['contextid'],
                    'instance' => $module['instance'],
                    'name' => $module['name'], 
                    'url' => $module['url'], 
                );
            }
        }

        $result = array();
        foreach ($course_modules as $cm) {
            $role_assignment = $DB->get_record('role_assignments', array(
                'roleid' => 3,
                'contextid' => $cm['contextid'],
                'userid' => $userid
            ));

            if ($role_assignment) {
                $intro = $DB->get_record('quiz', array('id' => $cm['instance']))->intro;

                $firstname = $DB->get_record('user', array('id' => $userid))->firstname;
                $lastname = $DB->get_record('user', array('id' => $userid))->lastname;
                $fullname = $firstname . ' ' . $lastname;

                $result[] = array(
                    'id' => $cm['instance'],
                    'name' => $cm['name'],
                    'intro' => $intro,
                    'total_questions' => $DB->count_records('question_references', array('usingcontextid' => $cm['contextid'])),
                    'url' => $cm['url'],
                    'created_by' => $fullname
                );
            }
        }

        return $result;
    }

    /**
     * Parameter description for create_sections().
     *
     * @return external_description
     */
    public static function get_quiz_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'id of the quiz'),
                'name' => new external_value(PARAM_TEXT, 'name of the quiz'),
                'intro' => new external_value(PARAM_TEXT, 'intro of the quiz'),
                'total_questions' => new external_value(PARAM_INT, 'total questions of the quiz'),
                'url' => new external_value(PARAM_TEXT, 'url for the quiz'),
                'created_by' => new external_value(PARAM_TEXT, 'creator of the quiz'),
            ])
        );
    }

    /**
     * Parameter description for get_roles().
     *
     * @return external_function_parameters.
     */
    public static function get_all_quiz_parameters(): external_function_parameters {
        return new external_function_parameters([
            // If this function had any parameters, they would be described here.
            // This example has no parameters, so the array is empty.
        ]);
    }

    public static function get_all_quiz() {
        global $DB;
        
        $url = 'http://colle.southeastasia.cloudapp.azure.com/moodle/webservice/rest/server.php?wstoken=1f95ee6650d2e1a6aa6e152f6bf4702c&wsfunction=core_course_get_contents&moodlewsrestformat=json&courseid=2';
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $modules = $data[0]['modules'];

        $course_modules = array();
        foreach ($modules as $module) {
            if($module['modname'] == 'quiz'){
                $course_modules[] = array(
                    'id' => $module['id'],
                    'contextid' => $module['contextid'],
                    'instance' => $module['instance'],
                    'name' => $module['name'], 
                    'url' => $module['url'], 
                );
            }
        }

        $result = array();
        foreach ($course_modules as $cm) {
            $role_assignments = $DB->get_record('role_assignments', array(
                'roleid' => 3,
                'contextid' => $cm['contextid'],
            ));
            
            if($role_assignments){
                $intro = $DB->get_record('quiz', array('id' => $cm['instance']))->intro;

                $firstname = $DB->get_record('user', array('id' => $role_assignments->userid))->firstname;
                $lastname = $DB->get_record('user', array('id' => $role_assignments->userid))->lastname;
                $fullname = $firstname . ' ' . $lastname;

                $result[] = array(
                    'id' => $cm['instance'],
                    'name' => $cm['name'],
                    'intro' => $intro,
                    'total_questions' => $DB->count_records('question_references', array('usingcontextid' => $cm['contextid'])),
                    'url' => $cm['url'],
                    'created_by' => $fullname
                );
            }
            
        }

        return $result;
    }

    /**
     * Parameter description for create_sections().
     *
     * @return external_description
     */
    public static function get_all_quiz_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'id of the quiz'),
                'name' => new external_value(PARAM_TEXT, 'name of the quiz'),
                'intro' => new external_value(PARAM_TEXT, 'intro of the quiz'),
                'total_questions' => new external_value(PARAM_INT, 'total questions of the quiz'),
                'url' => new external_value(PARAM_TEXT, 'url for the quiz'),
                'created_by' => new external_value(PARAM_TEXT, 'creator of the quiz'),
            ])
        );
    }

    /**
     * Describes the parameters for get_all_user_best_grades().
     *
     * @return external_function_parameters.
     * @since Moodle 4.3
     */
    public static function get_all_user_best_grades_parameters() {
        return new external_function_parameters (
            [
                'userid' => new external_value(PARAM_INT, 'user id, empty for current user', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Return a list of attempts for the given user.
     *
     * @param int $userid user id
     * @return array of warnings and the list of attempts
     * @since Moodle 4.3
     */
    public static function get_all_user_best_grades($userid = 0) {
        global $DB;
        $host = 'colle.southeastasia.cloudapp.azure.com';
        $token = '1f95ee6650d2e1a6aa6e152f6bf4702c';

        $attempts = $DB->get_records_sql(
            "SELECT quiz FROM {quiz_attempts} WHERE userid = :userid AND timefinish != 0",
            [
                'userid'  => $userid
            ]
        );
        foreach ($attempts as $attempt) {
            $quizids[] = $attempt->quiz;
        }
        if ($quizids != null) {
            $quizidsunique = array_unique($quizids);
            foreach ($quizidsunique as $quizid) {
                $url = "http://$host/moodle/webservice/rest/server.php?wstoken=$token&wsfunction=mod_quiz_get_user_attempts&moodlewsrestformat=json&quizid=$quizid&userid=$userid";
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                $highest_sumgrades = 0;
                $attemptid = 0;
                $instance = 0; // quiz id
                foreach ($data['attempts'] as $attempt) {
                    if ($attempt['sumgrades'] >= $highest_sumgrades) {
                        $highest_sumgrades = $attempt['sumgrades'];
                        $attemptid = $attempt['id'];
                        $instance = $attempt['quiz'];
                        $state = $attempt['state'];
                        $timefinish = $attempt['timefinish'];
                    }
                }
                $timefinishstr = date('Y-m-d H:i:s', $timefinish);
                $quiz_grades = $DB->get_record_sql(
                    "SELECT grade FROM {quiz_grades} WHERE userid = :userid AND quiz = :quizid",
                    [
                        'userid' => $userid,
                        'quizid' => $instance
                    ]
                );
    
                $url = "http://$host/moodle/webservice/rest/server.php?wstoken=$token&wsfunction=core_course_get_course_module_by_instance&moodlewsrestformat=json&module=quiz&instance=$instance";
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                $cmid = $data['cm']['id'];
                $cmname = $data['cm']['name'];
                $result[] = array(
                    'name' => $cmname,
                    'status' => $state,
                    'timefinish' => $timefinishstr,
                    'grade' => $quiz_grades->grade,
                    'url' => "http://$host/moodle/mod/quiz/review.php?attempt=$attemptid&cmid=$cmid"
                );
            }
        }
        else {
            $result[] = array(
                'name' => "",
                'status' => "Student has not finished any quiz.",
                'timefinish' => "",
                'grade' => 0.0,
                'url' => ""
            );
        }

        return $result;
    }

    /**
     * Describes the get_user_best_grades return value.
     *
     * @return external_multiple_structure
     * @since Moodle 4.3
     */
    public static function get_all_user_best_grades_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'quiz name'),
                'status' => new external_value(PARAM_TEXT, 'quiz status'),
                'timefinish' => new external_value(PARAM_TEXT, 'time finish'),
                'grade' => new external_value(PARAM_FLOAT, 'user best grades'),
                'url' => new external_value(PARAM_TEXT, 'review url')
            ])
        );
    }

    /**
     * Describes the parameters for get_user_best_grades_by_quiz().
     *
     * @return external_function_parameters.
     * @since Moodle 4.3
     */
    public static function get_user_best_grades_by_quiz_parameters() {
        return new external_function_parameters (
            [
                'quizid' => new external_value(PARAM_INT, 'quiz instance id'),
                'userid' => new external_value(PARAM_INT, 'user id, empty for current user', VALUE_DEFAULT, 0),

            ]
        );
    }

    /**
     * Return a list of attempts for the given quiz and user.
     *
     * @param int $quizid quiz instance id
     * @param int $userid user id
     * @return array of warnings and the list of attempts
     * @since Moodle 4.3
     */
    public static function get_user_best_grades_by_quiz($quizid, $userid = 0) {

        $host = 'colle.southeastasia.cloudapp.azure.com';
        $token = '1f95ee6650d2e1a6aa6e152f6bf4702c';

        $url = "http://$host/moodle/webservice/rest/server.php?wstoken=$token&wsfunction=mod_quiz_get_user_attempts&moodlewsrestformat=json&quizid=$quizid&userid=$userid";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $highest_sumgrades = 0;
        $attemptid = 0;
        $instance = 0; // quiz id

        foreach ($data['attempts'] as $attempt) {
            if ($attempt['sumgrades'] >= $highest_sumgrades) {
                $highest_sumgrades = $attempt['sumgrades'];
                $attemptid = $attempt['id'];
                $instance = $attempt['quiz'];
                $state = $attempt['state'];
                $timefinish = $attempt['timefinish'];
            }
        }

        $timefinishstr = date('Y-m-d H:i:s', $timefinish);

        $url = "http://$host/moodle/webservice/rest/server.php?wstoken=$token&wsfunction=core_course_get_course_module_by_instance&moodlewsrestformat=json&module=quiz&instance=$instance";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $cmid = $data['cm']['id'];
        $cmname = $data['cm']['name'];

        $result[] = array(
            'name' => $cmname,
            'status' => $state,
            'timefinish' => $timefinishstr,
            'sumgrades' => $highest_sumgrades,
            'url' => "http://$host/moodle/mod/quiz/review.php?attempt=$attemptid&cmid=$cmid"
        );

        return $result;
    }

    /**
     * Describes the get_user_best_grades_by_quiz return value.
     *
     * @return external_multiple_structure
     * @since Moodle 4.3
     */
    public static function get_user_best_grades_by_quiz_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'quiz name'),
                'status' => new external_value(PARAM_TEXT, 'quiz status'),
                'timefinish' => new external_value(PARAM_TEXT, 'time finish'),
                'sumgrades' => new external_value(PARAM_INT, 'user best grades'),
                'url' => new external_value(PARAM_TEXT, 'review url')
            ])
        );
    }

    /**
     * Parameter description for create_course().
     *
     * @return external_function_parameters.
     */
    public static function create_course_parameters() {
        return new external_function_parameters([
            'fullname' => new external_value(PARAM_TEXT, 'Nama lengkap kursus'),
            'shortname' => new external_value(PARAM_TEXT, 'Nama singkat kursus'),
            'enrolmentkey' => new external_value(PARAM_TEXT, 'Enrolment key for the course'),
            'summary' => new external_value(PARAM_TEXT, 'Summary of the course'),
            'userid' => new external_value(PARAM_INT, 'User id of the teacher'),
        ]);
    }

    /**
     * Create a new course and set enrolment key.
     *
     * @param string $fullname Full name of the course.
     * @param string $shortname Short name of the course.
     * @param string $enrolmentkey Enrolment key for the course.
     * @param string $summary  Summary of the course.
     * @return array Result of course creation.
     */
    public static function create_course($fullname, $shortname, $enrolmentkey, $summary, $userid) {
        global $DB;
        $host = 'colle.southeastasia.cloudapp.azure.com';
        $token = '1f95ee6650d2e1a6aa6e152f6bf4702c';
    
        $fullname_encoded = urlencode($fullname);
        $shortname_encoded = urlencode($shortname);
        $summary_encoded = urlencode($summary);
        $userid_encoded = urlencode($userid);

        $courseid1 = $DB->get_field_sql('SELECT MAX(id) FROM {course}');
    
        // Susun URL dengan nilai-nilai parameter yang sudah diencode
        $url = "http://$host/moodle/webservice/rest/server.php?wstoken=$token&wsfunction=core_course_create_courses&moodlewsrestformat=json&courses[0][fullname]=$fullname_encoded&courses[0][shortname]=$shortname_encoded&courses[0][summary]=$summary_encoded&&courses[0][categoryid]=1";
    
        $curl = curl_init($url);
        // Execute cURL request
        curl_exec($curl);
        curl_close($curl);

        $courseid2 = $DB->get_field_sql('SELECT MAX(id) FROM {course}');
        $result = array();

        if ($courseid1 === $courseid2) {
            $courseid = null;
            $result['message'] = 'Failed to update enrol table.';
        } else {
            $courseid = $courseid2;
        
            $enrol = $DB->get_record('enrol', array('enrol' => 'self', 'courseid' => $courseid));
        
            if ($enrol) {
                $enrol->status = 0;
                $enrol->password = $enrolmentkey;
        
                // Eksekusi update
                if (!$DB->update_record('enrol', $enrol)) {
                    $result['message'] = 'Failed to update enrol table.';
                } else {
                    $result['message'] = 'Enrol table updated successfully, User assigned.';
                    $url = "http://$host/moodle/webservice/rest/server.php?wstoken=$token&wsfunction=enrol_manual_enrol_users&moodlewsrestformat=json&enrolments[0][roleid]=3&enrolments[0][userid]=$userid_encoded&enrolments[0][courseid]=$courseid";
    
                    $curl = curl_init($url);
                    curl_exec($curl);
                    curl_close($curl);
                }
            } else {
                // Handle jika tidak ada objek enrol yang ditemukan
                $result['message'] = 'No enrol record found';
            }
        }
        
        $result['courseid'] = $courseid;

        return $result;
    }

    /**
     * Parameter description for create_course().
     *
     * @return external_description
     */
    public static function create_course_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'ID of the created course'),
                'message' => new external_value(PARAM_TEXT, 'Message regarding the course creation'),
            )
        );
    }
    
}
