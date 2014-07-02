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

class local_wsmiidle_enroll extends external_api {

    public static function enroll_user_course($eroll) {

        //validate parameters
        $params = self::validate_parameters(self::enroll_user_course_parameters(), array('eroll' => $eroll));

        return array(
            'id' => 0,
            'status' => 'success',
            'message' => 'Aluno matriculado no curso'
        );
    }
    public static function enroll_user_course_parameters() {
        return new external_function_parameters(
            array(
                'enroll' => new external_single_structure(
                    array(
                        'mat_id' => new external_value(PARAM_INT, 'Id da matricula na turma no gestor'),
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma'),
                        'mat_codigo' => new external_value(PARAM_INT, 'Codigo da matricula'),
                    )
                )
            )
        );
    }
    public static function enroll_user_course_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da matricula criada'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }

    public static function enroll_user_discipline($eroll) {

        //validate parameters
        $params = self::validate_parameters(self::enroll_user_discipline_parameters(), array('eroll' => $eroll));

        return array(
            'id' => 0,
            'status' => 'success',
            'message' => 'Aluno matriculado na disciplina'
        );
    }
    public static function enroll_user_discipline_parameters() {
        return new external_function_parameters(
            array(
                'enroll' => new external_single_structure(
                    array(
                        'mof_id' => new external_value(PARAM_INT, 'Id da matricula na disciplina no gestor'),
                        'ofd_id' => new external_value(PARAM_INT, 'Id da disciplina oferecida'),
                        'alu_id' => new external_value(PARAM_INT, 'Id do aluno'),
                    )
                )
            )
        );
    }
    public static function enroll_user_discipline_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da matricula criada'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
}