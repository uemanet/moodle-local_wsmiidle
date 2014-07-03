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

        // Valida os parametros.
        $params = self::validate_parameters(self::create_course_parameters(), array('course' => $course));

        // Inlcui a biblioteca de curso do moodle
        require_once("{$CFG->dirroot}/course/lib.php");

        // Transforma o array em objeto.
        $course = (object)$course;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do curso apartir do trm_id da turma.
        $courseid = self::get_course_by_trm_id($course->trm_id);

        // Dispara uma excessao se essa turma ja estiver mapeado para um curso.
        if($courseid) {
            throw new Exception("Essa turma ja esta mapeada com o curso de id: " . $courseid);
        }
        
        // Cria o curso usando a biblioteca do proprio moodle.
        $result = create_course($course);

        // Caso o curso tenha sido criado adiciona a tabela de controle os dados dos curso e da turma.
        if($result->id) {
            $data['trm_id'] = $course->trm_id;
            $data['courseid'] = $result->id;

            $res = $DB->insert_record('itg_turmas_cursos', $data);
        }

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        // Prepara o array de retorno.
        if($res) {
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
        global $CFG, $DB;

        // Valida os parametros.
        $params = self::validate_parameters(self::update_course_parameters(), array('course' => $course));

        // Inlcui a biblioteca de cursos do moodle
        require_once("{$CFG->dirroot}/course/lib.php");

        // Transforma o array em objeto.
        $course = (object)$course;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do curso apartir do trm_id da turma.
        $courseid = self::get_course_by_trm_id($course->trm_id);

        // Se nao existir curso mapeado para a turma dispara uma excessao.
        if($courseid) {
            $course->id = $courseid;
        } else {
            throw new Exception("Nenhum curso mapeado com a turma com trm_id: " . $course->trm_id);
        }

        // Cria o curso usando a biblioteca do proprio moodle.
        update_course($course);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        // Prepara o array de retorno.
        $returndata['id'] = $courseid;
        $returndata['status'] = 'success';
        $returndata['message'] = "Curso atualizado com sucesso";

        return $returndata;
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
    protected static function get_course_by_trm_id($trm_id) {
        global $DB;
        
        // Busca o id do curso apartir do trm_id da turma.
        $sql = "SELECT courseid FROM {itg_turmas_cursos} WHERE trm_id = :trm_id";
        $params['trm_id'] = $trm_id;
        $courseid = current($DB->get_records_sql($sql, $params));

        if($courseid) {
            $courseid = $courseid->courseid;
        } else {
            $courseid = 0;
        }

        return $courseid;
    }
}