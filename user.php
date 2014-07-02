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

class local_wsmiidle_user extends external_api {

    public static function create_student($student) {

        //validate parameters
        $params = self::validate_parameters(self::create_student_parameters(), array('student' => $student));

        return array(
            'id' => 0,
            'status' => 'success',
            'message' => 'Aluno criado com sucesso'
        );
    }
    public static function create_student_parameters() {
        return new external_function_parameters(
            array(
                'student' => new external_single_structure(
                    array(
                        'alu_id' => new external_value(PARAM_INT, 'Id do aluno no gestor'),
                        'firstname' => new external_value(PARAM_INT, 'Primeiro nome do aluno'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do aluno'),
                        'email' => new external_value(PARAM_TEXT, 'Email do aluno'),
                        'username' => new external_value(PARAM_INT, 'Usuario de acesso do aluno'),
                        'password' => new external_value(PARAM_TEXT, 'Senha do aluno'),
                        'city' => new external_value(PARAM_INT, 'Cidade do aluno')
                    )
                )
            )
        );
    }
    public static function create_student_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id do aluno criado'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
    public static function update_student($student) {

        //validate parameters
        $params = self::validate_parameters(self::update_student_parameters(), array('student' => $student));

        return array(
                'id' => 0,
                'status' => 'success',
                'message' => 'Aluno alterado com sucesso'
            );
    }
    public static function update_student_parameters() {
        return new external_function_parameters(
            array(
                'student' => new external_single_structure(
                    array(
                        'alu_id' => new external_value(PARAM_INT, 'Id do aluno no gestor'),
                        'firstname' => new external_value(PARAM_INT, 'Primeiro nome do aluno'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do aluno'),
                        'email' => new external_value(PARAM_TEXT, 'Email do aluno'),
                        'username' => new external_value(PARAM_INT, 'Usuario de acesso do aluno'),
                        'password' => new external_value(PARAM_TEXT, 'Senha do aluno'),
                        'city' => new external_value(PARAM_INT, 'Cidade do aluno')
                    )
                )
            )
        );
    }
    public static function update_student_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id do aluno atualizado'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }

    // // // // //
    // TEACHER  //
    // // // // //
    public static function create_teacher($teacher) {

        //validate parameters
        $params = self::validate_parameters(self::create_teacher_parameters(), array('teacher' => $teacher));

        return array(
            'id' => 0,
            'status' => 'success',
            'message' => 'Professor criado com sucesso'
        );
    }
    public static function create_teacher_parameters() {
        return new external_function_parameters(
            array(
                'teacher' => new external_single_structure(
                    array(
                        'prf_id' => new external_value(PARAM_INT, 'Id do professor no gestor'),
                        'firstname' => new external_value(PARAM_INT, 'Primeiro nome do professor'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do professor'),
                        'email' => new external_value(PARAM_TEXT, 'Email do professor'),
                        'username' => new external_value(PARAM_INT, 'Usuario de acesso do professor'),
                        'password' => new external_value(PARAM_TEXT, 'Senha do professor'),
                        'city' => new external_value(PARAM_INT, 'Cidade do professor')
                    )
                )
            )
        );
    }
    public static function create_teacher_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id do professor criado'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
    public static function update_teacher($teacher) {
        
        //validate parameters
        $params = self::validate_parameters(self::update_teacher_parameters(), array('teacher' => $teacher));

        return array(
                'id' => 0,
                'status' => 'success',
                'message' => 'Professor alterado com sucesso'
            );
    }
    public static function update_teacher_parameters($teacher) {
        return new external_function_parameters(
            array(
                'teacher' => new external_single_structure(
                    array(
                        'prf_id' => new external_value(PARAM_INT, 'Id do professor no gestor'),
                        'firstname' => new external_value(PARAM_INT, 'Primeiro nome do professor'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do professor'),
                        'email' => new external_value(PARAM_TEXT, 'Email do professor'),
                        'username' => new external_value(PARAM_INT, 'Usuario de acesso do professor'),
                        'password' => new external_value(PARAM_TEXT, 'Senha do professor'),
                        'city' => new external_value(PARAM_INT, 'Cidade do professor')
                    )
                )
            )
        );
    }
    public static function update_teacher_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Id do professor atualizado'),
                'status' => new external_value(PARAM_TEXT, 'Status da operacao'),
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
}