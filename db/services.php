<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwsmiidle
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_wsmiidle_hello_world' => array(
                'classname'   => 'local_wsmiidle_external',
                'methodname'  => 'hello_world',
                'classpath'   => 'local/wsmiidle/externallib.php',
                'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
                'type'        => 'read',
        ),
        'local_wsmiidle_create_course' => array(
                'classname'   => 'local_wsmiidle_course',
                'methodname'  => 'create_course',
                'classpath'   => 'local/wsmiidle/course.php',
                'description' => 'Creates new course.',
                'type'        => 'write',
        ),
        'local_wsmiidle_update_course' => array(
                'classname'   => 'local_wsmiidle_course',
                'methodname'  => 'update_course',
                'classpath'   => 'local/wsmiidle/course.php',
                'description' => 'Update a course.',
                'type'        => 'write',
        ),
        'local_wsmiidle_create_student' => array(
                'classname'   => 'local_wsmiidle_user',
                'methodname'  => 'create_student',
                'classpath'   => 'local/wsmiidle/user.php',
                'description' => 'Creates new student.',
                'type'        => 'write',
        ),
        'local_wsmiidle_update_student' => array(
                'classname'   => 'local_wsmiidle_user',
                'methodname'  => 'update_student',
                'classpath'   => 'local/wsmiidle/user.php',
                'description' => 'Update a student.',
                'type'        => 'write',
        ),
        'local_wsmiidle_create_teacher' => array(
                'classname'   => 'local_wsmiidle_user',
                'methodname'  => 'create_teacher',
                'classpath'   => 'local/wsmiidle/user.php',
                'description' => 'Creates new teacher.',
                'type'        => 'write',
        ),
        'local_wsmiidle_update_teacher' => array(
                'classname'   => 'local_wsmiidle_user',
                'methodname'  => 'update_teacher',
                'classpath'   => 'local/wsmiidle/user.php',
                'description' => 'Update a teacher.',
                'type'        => 'write',
        ),
        'local_wsmiidle_create_discipline' => array(
                'classname'   => 'local_wsmiidle_discipline',
                'methodname'  => 'create_discipline',
                'classpath'   => 'local/wsmiidle/discipline.php',
                'description' => 'Creates new discipline.',
                'type'        => 'write',
        ),
        'local_wsmiidle_update_discipline' => array(
                'classname'   => 'local_wsmiidle_discipline',
                'methodname'  => 'update_discipline',
                'classpath'   => 'local/wsmiidle/discipline.php',
                'description' => 'Update a discipline.',
                'type'        => 'write',
        ),
        'local_wsmiidle_enrol_user_course' => array(
                'classname'   => 'local_wsmiidle_enrol',
                'methodname'  => 'enrol_user_course',
                'classpath'   => 'local/wsmiidle/enrol.php',
                'description' => 'Enrol a user in a course.',
                'type'        => 'write',
        ),
        'local_wsmiidle_enrol_user_discipline' => array(
                'classname'   => 'local_wsmiidle_enrol',
                'methodname'  => 'enrol_user_discipline',
                'classpath'   => 'local/wsmiidle/enrol.php',
                'description' => 'Enrol a user in a discipline.',
                'type'        => 'write',
        ),
        'local_wsmiidle_unenrol_user_discipline' => array(
                'classname'   => 'local_wsmiidle_enrol',
                'methodname'  => 'unenrol_user_discipline',
                'classpath'   => 'local/wsmiidle/enrol.php',
                'description' => 'Unenrol a user in a discipline.',
                'type'        => 'write',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Miidle Web Service' => array(
                'functions' => array(
                                'local_wsmiidle_hello_world',
                                'local_wsmiidle_create_course',
                                'local_wsmiidle_update_course',
                                'local_wsmiidle_create_student',
                                'local_wsmiidle_update_student',
                                'local_wsmiidle_create_teacher',
                                'local_wsmiidle_update_teacher',
                                'local_wsmiidle_create_discipline',
                                'local_wsmiidle_update_discipline',
                                'local_wsmiidle_enrol_user_course',
                                'local_wsmiidle_enrol_user_discipline',
                                'local_wsmiidle_unenrol_user_discipline'
                ),
                'restrictedusers' => 1,
                'enabled'=>1,
        )
);
