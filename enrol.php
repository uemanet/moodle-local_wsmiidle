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

require_once("base.php");

class local_wsmiidle_enrol extends wsmiidle_base {

    public static function enrol_user_course($enrol) {

        //validate parameters
        $params = self::validate_parameters(self::enrol_user_course_parameters(), array('enrol' => $enrol));

        $enrol = (object)$enrol;

        // Busca o id do curso apartir do trm_id da turma.
        $courseid = self::get_course_by_trm_id($enrol->trm_id);
        // Dispara uma excessao se essa turma nao estiver mapeada para um curso.
        if(!$courseid) {
            throw new Exception("Não existe curso mapeado para a turma que esse aluno foi matriculado. trm_id: " . $enrol->trm_id);
        }

        // Busca o id do usuario apartir do alu_id do aluno.
        $userid = self::get_user_by_alu_id($enrol->alu_id);
        // Dispara uma excessao se esse aluno nao estiver mapeado para um usuario.
        if(!$userid) {
            throw new Exception("Nenhum usuario esta mapeado para o aluno com alu_id: " . $enrol->alu_id);
        }

        self::enrol_user_in_moodle_course($userid, $courseid, self::STUDENT_ROLEID);

        return array(
            'id' => 0,
            'status' => 'success',
            'message' => 'Aluno matriculado no curso'
        );
    }
    public static function enrol_user_course_parameters() {
        return new external_function_parameters(
            array(
                'enrol' => new external_single_structure(
                    array(
                        'mat_id' => new external_value(PARAM_INT, 'Id da matricula na turma no gestor'),
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma'),
                        'alu_id' => new external_value(PARAM_INT, 'Id do aluno'),
                        'mat_codigo' => new external_value(PARAM_TEXT, 'Codigo da matricula')
                    )
                )
            )
        );
    }
    public static function enrol_user_course_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da matricula criada'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }

    public static function enrol_user_discipline($enrol) {
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::enrol_user_discipline_parameters(), array('enrol' => $enrol));

        $enrol = (object)$enrol;

        // Busca a seccao apartir do id da oferta da disciplina.
        $section = self::get_section_by_ofd_id($enrol->ofd_id);
        // Dispara uma excessao caso nao tenha um mapeamento entre a oferta da disciplina e uma section.
        if(!$section) {
            throw new Exception("Nao existe uma section mapeada para essa disciplina oferecida. ofd_id: " . $enrol->ofd_id);
        }

        // Busca o id do usuario apartir do alu_id do aluno.
        $userid = self::get_user_by_alu_id($enrol->alu_id);
        // Dispara uma excessao se esse aluno nao estiver mapeado para um usuario.
        if(!$userid) {
            throw new Exception("Nenhum usuario esta mapeado para o aluno com alu_id: " . $enrol->alu_id);
        }

        // Verifica se o aluno ja esta matriculado para a disciplina
        $userdiscipline = $DB->get_record('itg_user_discipline', array('userid'=>$userid, 'sectionid'=>$section->sectionid), '*');
        if($userdiscipline) {
            throw new Exception("O aluno ja esta matriculado para essa disciplina. ofd_id: " . $enrol->ofd_id);
        }

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        $data['mof_id'] = $enrol->mof_id;
        $data['userid'] = $userid;
        $data['sectionid'] = $section->sectionid;

        $res = $DB->insert_record('itg_user_discipline', $data);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        return array(
            'id' => $res,
            'status' => 'success',
            'message' => 'Aluno matriculado na disciplina'
        );
    }
    public static function enrol_user_discipline_parameters() {
        return new external_function_parameters(
            array(
                'enrol' => new external_single_structure(
                    array(
                        'mof_id' => new external_value(PARAM_INT, 'Id da matricula na disciplina no gestor'),
                        'ofd_id' => new external_value(PARAM_INT, 'Id da disciplina oferecida'),
                        'alu_id' => new external_value(PARAM_TEXT, 'Id do aluno'),
                    )
                )
            )
        );
    }
    public static function enrol_user_discipline_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da matricula criada'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
    public static function unenrol_user_discipline($unenrol) {
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::unenrol_user_discipline_parameters(), array('unenrol' => $unenrol));

        $unenrol = (object)$unenrol;

        // Verifica se a matricula na disciplina existe
        $userdiscipline = $DB->get_record('itg_user_discipline', array('mof_id'=>$unenrol->mof_id, '*'));
        if(!$userdiscipline) {
            throw new Exception("Nao exsite mapeamento para essa matricula na oferta disciplina. mof_id: " . $unenrol->mof_id);
        }

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();
        
        $DB->delete_records('itg_user_discipline', array('id'=>$userdiscipline->id));

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        return array(
            'id' => $userdiscipline->id,
            'status' => 'success',
            'message' => 'Aluno desmatriculado da disciplina'
        );
    }
    public static function unenrol_user_discipline_parameters() {
        return new external_function_parameters(
            array(
                'unenrol' => new external_single_structure(
                    array(
                        'mof_id' => new external_value(PARAM_INT, 'Id da matricula na disciplina no gestor')
                    )
                )
            )
        );
    }
    public static function unenrol_user_discipline_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da matricula removida'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
}