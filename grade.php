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

class local_wsmiidle_grade extends wsmiidle_base {
    public static function get_grades_batch($grades) {
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::get_grades_batch_parameters(), array('grades' => $grades));

        foreach ($grades as $g => $grade) {
            // Busca o id do usuario apartir do alu_id do aluno.
            $userid = self::get_user_by_alu_id($grade['alu_id']);

            if($userid) {
                $nota = self::get_grade_by_itemid($grade['itemid'], $userid);
                $gradeRetorno[] = array(
                    'alu_id' => $grade['alu_id'],
                    'itemid' => $grade['itemid'],
                    'tiponota' => $grade['tiponota'],
                    'grade' => $nota,
                    'status' => 'success',
                    'message' => 'Nota recebida com sucesso'
                );
            } else {
                $gradeRetorno[] = array(
                    'alu_id' => $grade['alu_id'],
                    'itemid' => $grade['itemid'],
                    'tiponota' => $grade['tiponota'],
                    'grade' => 0,
                    'status' => 'warning',
                    'message' => 'Aluno nao mapeado a um usuario do moodle'
                );
            }
        }

        return $gradeRetorno;
    }
    public static function get_grades_batch_parameters() {
        return new external_function_parameters(
            array(
                'grades' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'alu_id' => new external_value(PARAM_INT, 'Id do aluno'),
                            'itemid' => new external_value(PARAM_INT, 'Id do item de nota da atividade'),
                            'tiponota' => new external_value(PARAM_TEXT, 'Tipo de nota')
                        )
                    )
                )
            )
        );
    }
    public static function get_grades_batch_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'alu_id' => new external_value(PARAM_INT, 'Id do aluno no academico'),
                    'itemid' => new external_value(PARAM_INT, 'Id do item de nota'),
                    'tiponota' => new external_value(PARAM_TEXT, 'Tipo de nota'),
                    'grade' => new external_value(PARAM_TEXT, 'Nota do aluno no item'),
                    'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                    'message' => new external_value(PARAM_TEXT, 'Mensagem da operacao')
                )
            )
        );
    }
    protected static function get_grade_by_itemid($itemid, $userid){
        global $DB;

        $finalgrade = 0;

        $sql = "SELECT gg.*, gi.scaleid 
                FROM {grade_grades} gg
                INNER JOIN {grade_items} gi ON gi.id = gg.itemid
                WHERE userid = :userid
                AND itemid = :itemid";

        $grade = $DB->get_record_sql($sql, array('userid' => $userid, 'itemid' => $itemid));

        // Retorna 0 caso nao seja encontrados registros
        if(!$grade) {
            return 0;
        }

        // Caso a nota tenha escala vai buscar a nota final
        if($grade->scaleid) {
            return self::get_grade_by_scale($grade->scaleid, $grade->finalgrade);
        }

        // Formata a nota final
        $finalgrade = number_format($grade->finalgrade, 2);

        // Caso a nota maxima seja maior que 10 formata para o valor adequado
        if($grade->rawgrademax > 10 && $grade->finalgrade > 1) {
            $finalgrade = ($grade->finalgrade - 1) / $grade->rawgrademax;
            $finalgrade = number_format($finalgrade, 2);
        }

        return $finalgrade;
    }
    protected static function get_grade_by_scale($scaleid, $grade) {
        global $DB;

        $scale = $DB->get_record('scale', array('id'=>$scaleid), '*');

        $scale = $scale->scale;
        // $scale = str_replace(' ', '', $scale);

        $scale_arr = explode(', ', $scale);

        $grade = (int)$grade - 1;

        return $scale_arr[$grade];
    }
}