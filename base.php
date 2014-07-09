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
    protected static function get_course_by_trm_id($trm_id) {
        global $DB;
        
        // Busca o id do curso apartir do trm_id da turma.
        $sql = "SELECT courseid FROM {itg_turma_course} WHERE trm_id = :trm_id";
        $params['trm_id'] = $trm_id;
        $courseid = current($DB->get_records_sql($sql, $params));

        if($courseid) {
            $courseid = $courseid->courseid;
        } else {
            $courseid = 0;
        }

        return $courseid;
    }
    protected static function get_section_by_ofd_id($ofd_id){
        global $DB;
        
        // Busca o id da seccao apartir do id da oferta da disciplina
        $sql = "SELECT sectionid FROM {itg_disciplina_section} WHERE ofd_id = :ofd_id";
        $params['ofd_id'] = $ofd_id;
        $sectionid = current($DB->get_records_sql($sql, $params));

        if($sectionid) {
            $sectionid = $sectionid->sectionid;
        } else {
            $sectionid = 0;
        }

        return $sectionid;
    }
    protected static function find_user_by_prf_id($prf_id) {
        global $DB;
        
        // Busca o id do usuario apartir do prf_id do professor.
        $sql = "SELECT userid FROM {itg_professor_user} WHERE prf_id = :prf_id";
        $params['prf_id'] = $prf_id;
        $userid = current($DB->get_records_sql($sql, $params));

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
}