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
 * Web service definitions for local_colle
 *
 * @package    local_colle
 * @copyright  2020 corvus albus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_colle_create_quiz' => array(
         'classname' => 'local_colle_external',
         'methodname' => 'create_quiz',
         'classpath' => 'local/colle/externallib.php',
         'description' => 'Create quiz via Web Service API.',
         'type' => 'write',
         'capabilities' => '',
    ),
    'local_colle_get_context_id' => array(
        'classname' => 'local_colle_external',
        'methodname' => 'get_context_id',
        'classpath' => 'local/colle/externallib.php',
        'description' => 'Get context id from module Web Service API.',
        'type' => 'read',
        'capabilities' => '',
    ),
    'local_colle_get_quiz' => array(
        'classname' => 'local_colle_external',
        'methodname' => 'get_quiz',
        'classpath' => 'local/colle/externallib.php',
        'description' => 'Get created quiz by user Web Service API.',
        'type' => 'read',
        'capabilities' => '',
    ),
    'local_colle_get_all_quiz' => array(
        'classname' => 'local_colle_external',
        'methodname' => 'get_all_quiz',
        'classpath' => 'local/colle/externallib.php',
        'description' => 'Get all quiz Web Service API.',
        'type' => 'read',
        'capabilities' => '',
    ),
    'local_colle_get_all_user_best_grades' => array(
        'classname' => 'local_colle_external',
        'methodname' => 'get_all_user_best_grades',
        'classpath' => 'local/colle/externallib.php',
        'description' => 'Return a list of attempts for the given user.',
        'type' => 'read',
        'capabilities' => '',
    ),
    'local_colle_get_user_best_grades_by_quiz' => array(
        'classname' => 'local_colle_external',
        'methodname' => 'get_user_best_grades_by_quiz',
        'classpath' => 'local/colle/externallib.php',
        'description' => 'Return a best grades for the given user and quiz.',
        'type' => 'read',
        'capabilities' => '',
    ),
    'local_colle_create_course' => array(
        'classname' => 'local_colle_external',
        'methodname' => 'create_course',
        'classpath' => 'local/colle/externallib.php',
        'description' => 'Create a new course and set enrolment key.',
        'type' => 'write',
        'capabilities' => '',
    ), 
);