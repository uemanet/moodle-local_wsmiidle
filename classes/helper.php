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
 * Miidle Web Service - helper manager
 *
 * @package    wsmiidle
 * @copyright  2014 Willian Mano (http://willianmano.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_wsmiidle_helper {

    /**
     * Observe the events, and dispatch them if necessary.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function observer(\core\event\base $event) {
        // var_dump($event);
        // exit;
        switch ($event->eventname) {
            case '\mod_assign\event\submission_status_viewed':
            case '\mod_assign\event\submission_form_viewed':
                self::verifyEvent($event);
            break;
        }

        if($event instanceof \core\event\course_module_viewed) {
            self::verifyEvent($event);
        }
    }
    protected static function verifyEvent($event) {
        if(is_siteadmin()) {
            return;
        }
        // So executa as acoes de bloqueio caso seja aluno
        $context = context_course::instance($event->courseid);
        $userRoles = get_user_roles($context, $event->userid);

        $isStudent = false;
        if(!empty($userRoles)) {
            foreach ($userRoles as $r => $role) {
                if($role->roleid == 5) {
                    $isStudent = true;
                    break;
                }
            }
        }

        if ($isStudent) {
            $manager = local_wsmiidle_manager::get($event->courseid);
            $manager->capture_event($event);
        }
    }
}
