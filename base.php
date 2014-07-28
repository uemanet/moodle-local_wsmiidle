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
 * Miidle Web Service
 *
 * @package    wsmiidle
 * @copyright  2014 Willian Mano (http://willianmano.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class wsmiidle_base extends external_api {
    
    const TEACHER_ROLEID = 3;
    const STUDENT_ROLEID = 5;

    protected static function get_course_by_trm_id($trm_id) {
        global $DB;
        
        $courseid = $DB->get_record('itg_turma_course', array('trm_id'=>$trm_id), '*');

        if($courseid) {
            $courseid = $courseid->courseid;
        } else {
            $courseid = 0;
        }

        return $courseid;

    }
    protected static function get_section_by_ofd_id($ofd_id){
        global $DB;
        
        $section = $DB->get_record('itg_disciplina_section', array('ofd_id'=>$ofd_id), '*');

        return $section;
    }
    protected static function get_user_by_alu_id($alu_id) {
        global $DB;
        
        $userid = $DB->get_record('itg_aluno_user', array('alu_id'=>$alu_id), '*');

        if($userid) {
            $userid = $userid->userid;
        } else {
            $userid = 0;
        }

        return $userid;
    }
    protected static function get_user_by_prf_id($prf_id) {
        global $DB;
        
        $userid = $DB->get_record('itg_professor_user', array('prf_id'=>$prf_id), '*');

        if($userid) {
            $userid = $userid->userid;
        } else {
            $userid = 0;
        }

        return $userid;
    }
    protected static function get_course_enrol($courseid) {
        global $DB;
        
        $enrol = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'manual'), '*', MUST_EXIST);

        return $enrol;
    }
    protected static function enrol_user_in_moodle_course($userid, $courseid, $roleid) {
        global $CFG;

        $courseenrol = self::get_course_enrol($courseid);

        require_once($CFG->libdir . "/enrollib.php");

        if (!$enrol_manual = enrol_get_plugin('manual')) {
            throw new coding_exception('Can not instantiate enrol_manual');
        }

        $enrol_manual->enrol_user($courseenrol, $userid, $roleid, time());
    }
    protected static function unenrol_user_course($userid, $courseid) {
        global $CFG;

        $courseenrol = self::get_course_enrol($courseid);

        require_once($CFG->libdir . "/enrollib.php");

        if (!$enrol_manual = enrol_get_plugin('manual')) {
            throw new coding_exception('Can not instantiate enrol_manual');
        }

        $enrol_manual->unenrol_user($courseenrol, $userid);
    }
    protected static function get_user_discipline($userid, $sectionid) {
        global $DB;
        
        $userdiscipline = $DB->get_record('itg_user_discipline', array('userid'=>$userid, 'sectionid'=>$sectionid), '*');

        return $userdiscipline;
    }
    protected static function get_group_by_grp_id($grp_id) {
        global $DB;
        
        $group = $DB->get_record('itg_grupo_group', array('grp_id'=>$grp_id), '*');

        return $group;
    }
}
