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

class local_wsmiidle_discipline extends external_api {

    public static function create_discipline($discipline) {

        //validate parameters
        $params = self::validate_parameters(self::create_discipline_parameters(), array('discipline' => $discipline));

        return array(
            'id' => 0,
            'status' => 'success',
            'message' => 'Disciplina criada com sucesso'
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

        //validate parameters
        $params = self::validate_parameters(self::update_discipline_parameters(), array('discipline' => $discipline));

        return array(
                'id' => 0,
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