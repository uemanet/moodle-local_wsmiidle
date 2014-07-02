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

class local_wsmiidle_course extends external_api {

    public static function create_course($course) {
        global $CFG, $DB;

        //validate parameters
        $params = self::validate_parameters(self::create_course_parameters(), array('course' => $course));

        require_once("{$CFG->dirroot}/course/lib.php");

        $course = (object)$course;

        $trm_id = $course->trm_id;
        unset($course->trm_id);

        // $transaction = $DB->start_delegated_transaction();
        // $result = create_course($course);
        // $transaction->allow_commit();

        if($result->id) {
            $returndata['id'] = $result->id;
            $returndata['status'] = 'success';
            $returndata['message'] = 'Curso criado com sucesso';
        } else {
            $returndata['id'] = 0;
            $returndata['status'] = 'error';
            $returndata['message'] = 'Erro ao tentar criar o curso';
        }

        return $returndata;
    }
    public static function create_course_parameters() {
        return new external_function_parameters(
            array(
                'course' => new external_single_structure(
                    array(
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma no gestor'),
                        'category' => new external_value(PARAM_INT, 'Categoria do curso'),
                        'shortname' => new external_value(PARAM_TEXT, 'Nome curto do curso'),
                        'fullname' => new external_value(PARAM_TEXT, 'Nome completo do curso'),
                        'summaryformat' => new external_value(PARAM_INT, 'Formato do sumario'),
                        'format' => new external_value(PARAM_TEXT, 'Formato do curso'),
                        'numsections' => new external_value(PARAM_INT, 'Quantidade de sections'),
                        'integrado' => new external_value(PARAM_INT, 'Controle da integracao do curso')
                    )
                )
            )
        );
    }
    public static function create_course_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id do curso criado'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
    public static function update_course($course) {

        //validate parameter
        $params = self::validate_parameters(self::create_course_parameters(), array('course' => $course));
        
        return array(
                'id' => 0,
                'status' => 'success',
                'message' => 'Curso alterado com sucesso'
            );
    }
    public static function update_course_parameters() {
        return new external_function_parameters(
            array(
                'course' => new external_single_structure(
                    array(
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma no gestor'),
                        'category' => new external_value(PARAM_INT, 'Categoria do curso'),
                        'shortname' => new external_value(PARAM_TEXT, 'Nome curto do curso'),
                        'fullname' => new external_value(PARAM_TEXT, 'Nome completo do curso'),
                        'numsections' => new external_value(PARAM_INT, 'Quantidade de sections')
                    )
                )
            )
        );
    }
    public static function update_course_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id of updated course'),
                'status' => new external_value(PARAM_TEXT, 'Operation return status'),
                'message' => new external_value(PARAM_TEXT, 'Operation return message')
            )
        );
    }
}