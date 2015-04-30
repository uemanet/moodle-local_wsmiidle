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
 * Miidle Web Service - manager
 *
 * @package    wsmiidle
 * @copyright  2014 Willian Mano (http://willianmano.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_wsmiidle_manager {

    /** @var array Array of singletons. */
    protected static $instances;

    /** @var int Course ID. */
    protected $courseid = null;

    protected $course = null;

    /**
     * Constructor
     *
     * @param int $courseid The course ID.
     * @return void
     */
    protected function __construct($courseid) {
        global $DB;

        $this->courseid = $courseid;
        $this->course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
    }

    /**
     * Capture an event.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public function capture_event(\core\event\base $event) {
        global $DB, $CFG;

        if ($event->courseid !== $this->courseid) {
            throw new coding_exception('Event course ID does not match event course ID');
        }

        // The capture has not been enabled yet.
        if (!$this->is_enabled($event->userid)) {
            return;
        }

        // Verifica se a atividade esta em uma section mapeada    
        $sql = "SELECT cs.id as sectionid
                FROM {course_modules} cm
                INNER JOIN {course_sections} cs ON cm.section = cs.id
                INNER JOIN {itg_disciplina_section} dc ON dc.sectionid = cs.id
                WHERE cm.id = :instanceid";

        $params['instanceid'] = $event->contextinstanceid;
        $section = $DB->get_record_sql($sql, $params);

        if(!$section) {
            return;
        }

        // Verifica se o aluno esta matriculado na disciplina
        $isEnrolled = $DB->count_records('itg_user_discipline', array('sectionid' => $section->sectionid, 'userid' => $event->userid));

        if(!$isEnrolled) {
            
            $redirecttime = 5;
            if(in_array($event->eventname, local_wsmiidle_helper::$lookupeventswithoutredirecttime)) {
                $redirecttime = 0;
            }

            redirect(new moodle_url('/course/view.php',
                    array('id' => $this->courseid)), 'Você não esta matriculado na disciplina desta atividade.',$redirecttime);
        }
    }

    /**
     * Get an instance of the manager.
     *
     * @param int $courseid The course ID.
     * @param bool $forcereload Force the reload of the singleton, to invalidate local cache.
     * @return local_wsmiidle_manager The instance of the manager.
     */
    public static function get($courseid, $forcereload = false) {
        if ($forcereload || !isset(self::$instances[$courseid])) {
            self::$instances[$courseid] = new local_wsmiidle_manager($courseid);
        }
        return self::$instances[$courseid];
    }

    /**
     * Return the current course ID.
     *
     * @return int The course ID.
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Is the block enabled on the course?
     *
     * @return boolean True if enabled.
     */
    public function is_enabled($userid) {
        if($this->course->integrado) {
            return true;
        }

        return false;
    }
}
