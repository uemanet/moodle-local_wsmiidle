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

class local_wsmiidle_discipline extends wsmiidle_base {

    public static function create_discipline($discipline) {
        global $CFG, $DB;

        //validate parameters
        $params = self::validate_parameters(self::create_discipline_parameters(), array('discipline' => $discipline));

        $discipline = (object)$discipline;

        // Busca o id da seccao apartir do id da oferta da disciplina.
        $section = self::get_section_by_ofd_id($discipline->ofd_id);
        // Dispara uma excessao caso ja tenha um mapeamento entre a oferta da disciplina e uma section.
        if($section) {
            throw new Exception("Já existe uma section mapeada para essa disciplina oferecida. ofd_id: " . $discipline->ofd_id);
        }

        // Busca o id do curso apartir do trm_id da turma.
        $courseid = self::get_course_by_trm_id($discipline->trm_id);
        // Dispara uma excessao se essa turma ja estiver mapeado para um curso.
        if(!$courseid) {
            throw new Exception("Não existe curso mapeado para a turma onde essa disciplina foi oferecida. trm_id: " . $discipline->trm_id);
        }

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Pega o numero da ultima section do curso
        $sql = "SELECT section FROM {course_sections} WHERE course = :courseid ORDER BY section DESC LIMIT 1";
        $params['courseid'] = $courseid;
        $lastsection = current($DB->get_records_sql($sql, $params));

        // Ultima section do curso
        $lastsection = $lastsection->section;

        // Insere nova section no curso.
        $section['course'] = $courseid;
        $section['section'] = $lastsection + 1;
        $section['name'] = $discipline->name;
        $section['summaryformat'] = 1;
        $section['visible'] = 1;
        $section['id'] = $DB->insert_record('course_sections', $section);

        // Busca as configuracoes do formato do curso
        $courseformatoptions = $DB->get_record('course_format_options', array('courseid'=>$courseid, 'name' => 'numsections'), '*');

        // Atualiza o total de sections do curso
        $courseformatoptions->value = $lastsection + 1;        
        $DB->update_record('course_format_options', $courseformatoptions);

        if ($discipline->prf_id) {
            $userid = self::get_user_by_prf_id($discipline->prf_id);

            // Dispara uma excessao caso o professor nao esteja mapeado ainda
            if(!$userid) {
                throw new Exception("Não existe usuario mapeado para esse professor . prf_id: " . $discipline->prf_id);
            }

            // Matricula o usuario no curso.
            self::enrol_user_in_moodle_course($userid, $courseid, self::TEACHER_ROLEID);
        }

        // Adiciona a tabela de controle os dados da oferta da disciplina e section.
        $data['ofd_id'] = $discipline->ofd_id;
        $data['sectionid'] = $section['id'];
        $data['prf_id'] = $discipline->prf_id;
        $res = $DB->insert_record('itg_disciplina_section', $data);

        // Recria o cache do curso
        require_once($CFG->libdir . "/modinfolib.php");
        rebuild_course_cache($courseid, true);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        return array(
            'id' => $section['id'],
            'status' => 'success',
            'message' => 'Disciplina cadastrada com sucesso'
        );
    }
    public static function create_discipline_parameters() {
        return new external_function_parameters(
            array(
                'discipline' => new external_single_structure(
                    array(
                        'ofd_id' => new external_value(PARAM_INT, 'Id da disciplina oferecida no gestor'),
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma que a disciplina foi oferecida'),
                        'prf_id' => new external_value(PARAM_INT, 'Id do professor da disciplina'),
                        'name' => new external_value(PARAM_TEXT, 'Nome da disciplina')
                    )
                )
            )
        );
    }
    public static function create_discipline_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da disciplina criada'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
    public static function update_discipline($discipline) {
        global $DB, $CFG;

        //validate parameters
        $params = self::validate_parameters(self::update_discipline_parameters(), array('discipline' => $discipline));

        $discipline = (object)$discipline;

        // Busca o id da seccao apartir do id da oferta da disciplina.
        $section = self::get_section_by_ofd_id($discipline->ofd_id);
        // Dispara uma excessao caso ja tenha um mapeamento entre a oferta da disciplina e uma section.
        if(!$section) {
            throw new Exception("Não existe uma section mapeada para essa disciplina oferecida. ofd_id: " . $discipline->ofd_id);
        }

        // Busca o id do curso apartir do trm_id da turma.
        $courseid = self::get_course_by_trm_id($discipline->trm_id);
        // Dispara uma excessao se essa turma ja estiver mapeado para um curso.
        if(!$courseid) {
            throw new Exception("Não existe curso mapeado para a turma onde essa disciplina foi oferecida. trm_id: " . $discipline->trm_id);
        }

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        if($discipline->prf_id != 0) {
            if($discipline->prf_id != $section->prf_id) {
                if($section->prf_id != 0) {
                    // Remover professor antigo
                    $oldteacherid = self::get_user_by_prf_id($section->prf_id);
                    self::unenrol_user_course($oldteacherid, $courseid);
                }
                // adiciona novo professor
                $newteacherid = self::get_user_by_prf_id($discipline->prf_id);
                self::enrol_user_in_moodle_course($newteacherid, $courseid, self::TEACHER_ROLEID);

                $section->prf_id = $discipline->prf_id;
            }
        }
        // Professor foi removido da disciplina
        else if($discipline->prf_id == 0 && $section->prf_id != 0) {
            // Remover professor antigo
            $oldteacherid = self::get_user_by_prf_id($section->prf_id);
            self::unenrol_user_course($oldteacherid, $courseid);

            $section->prf_id = 0;
        }

        // Atualiza o controle da integracao
        $DB->update_record('itg_disciplina_section', $section);

        //Atualiza a section do curso
        $coursesection = $DB->get_record('course_sections', array('id'=>$section->sectionid), '*', MUST_EXIST);
        if($coursesection->name != $discipline->name) {
            $coursesection->name = $discipline->name;
            $DB->update_record('course_sections', $coursesection);

            // Recria o cache do curso
            require_once($CFG->libdir . "/modinfolib.php");
            rebuild_course_cache($courseid, true);
        }

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        return array(
                'id' => $section->sectionid,
                'status' => 'success',
                'message' => 'Disciplina alterada com sucesso'
            );
    }
    public static function update_discipline_parameters() {
        return new external_function_parameters(
            array(
                'discipline' => new external_single_structure(
                    array(
                        'ofd_id' => new external_value(PARAM_INT, 'Id da disciplina oferecida no gestor'),
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma que a disciplina foi oferecida'),
                        'prf_id' => new external_value(PARAM_INT, 'Id do professor da disciplina'),
                        'name' => new external_value(PARAM_TEXT, 'Nome da disciplina')
                    )
                )
            )
        );
    }
    public static function update_discipline_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id da disciplina alterada'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
}