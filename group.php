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

class local_wsmiidle_group extends wsmiidle_base {
    public static function create_group($group) {
        global $DB, $CFG;

        // Valida os parametros.
        $params = self::validate_parameters(self::create_group_parameters(), array('group' => $group));

        // Transforma o array em objeto.
        $group = (object)$group;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do curso apartir do trm_id da turma.
        $courseid = self::get_course_by_trm_id($group->trm_id);

        // Se nao existir curso mapeado para a turma dispara uma excessao.
        if(!$courseid) {
            throw new Exception("Nenhum curso mapeado com a turma com trm_id: " . $group->trm_id);
        }

        $groupbyname = self::get_group_by_name($courseid, $group->name);

        // Dispara uma excessao caso ja exista um grupo com o mesmo nome no mesmo curso
        if($groupbyname) {
            throw new Exception("ja existe um grupo com o mesmo nome nessa turma trm_id: " . $group->trm_id);
        }

        $groupdata['courseid'] = $courseid;
        $groupdata['name'] = $group->name;
        $groupdata['description'] = $group->description;
        $groupdata['descriptionformat'] = 1;
        $groupdata['timecreated'] = time();
        $groupdata['timemodified'] = $groupdata['timecreated'];

        $resultid = $DB->insert_record('groups', $groupdata);

        // Caso o curso tenha sido criado adiciona a tabela de controle os dados dos curso e da turma.
        if($resultid) {
            $data['trm_id'] = $group->trm_id;
            $data['grp_id'] = $group->grp_id;
            $data['groupid'] = $resultid;

            $res = $DB->insert_record('itg_grupo_group', $data);

            // Busca as configuracoes do curso
            $courseoptions = $DB->get_record('course', array('id'=>$courseid), '*');

            // Altera o formato de grupos do curso
            $courseoptions->groupmode = 1;
            $courseoptions->groupmodeforce = 1;
            $DB->update_record('course', $courseoptions);

            // Invalidate the grouping cache for the course
            cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));

            // Prepara o array de retorno.
            $returndata = null;
            if($res) {
                $returndata['id'] = $resultid;
                $returndata['status'] = 'success';
                $returndata['message'] = 'Grupo criado com sucesso';
            } else {
                $returndata['id'] = 0;
                $returndata['status'] = 'error';
                $returndata['message'] = 'Erro ao tentar criar o grupo';
            }
        }

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        return $returndata;
    }
    public static function create_group_parameters() {
        return new external_function_parameters(
            array(
                'group' => new external_single_structure(
                    array(
                        'trm_id' => new external_value(PARAM_INT, 'Id da turma do grupo no gestor'),
                        'grp_id' => new external_value(PARAM_INT, 'Id do grupo no gestor'),
                        'name' => new external_value(PARAM_TEXT, 'Nome do grupo'),
                        'description' => new external_value(PARAM_TEXT, 'Descrição do grupo')
                    )
                )
            )
        );
    }
    public static function create_group_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id do grupo criado'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
    private static function get_group_by_name($courseid, $name) {
        global $DB;
        
        $group = $DB->get_record('groups', array('courseid'=>$courseid, 'name' => $name), '*');

        return $group;
    }
}