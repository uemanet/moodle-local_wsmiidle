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
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::create_student_parameters(), array('student' => $student));

        // Transforma o array em objeto.
        $student = (object)$student;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do usuario apartir do alu_id do aluno.
        $userid = self::find_user_by_alu_id($student->alu_id);

        // Dispara uma excessao se esse aluno ja estiver mapeado para um usuario.
        if($userid) {
            throw new Exception("Essa aluno ja esta mapeado com o usuario de id: " . $userid);
        }
        
        // Cria o usuario usando a biblioteca do proprio moodle.
        $userid = self::save_student($student);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        // Prepara o array de retorno.
        if($userid) {
            $returndata['id'] = $userid;
            $returndata['status'] = 'success';
            $returndata['message'] = 'Usuario criado com sucesso';
        } else {
            $returndata['id'] = 0;
            $returndata['status'] = 'error';
            $returndata['message'] = 'Erro ao tentar criar o usuario';
        }

        return $returndata;
    }
    public static function create_student_parameters() {
        return new external_function_parameters(
            array(
                'student' => new external_single_structure(
                    array(
                        'alu_id' => new external_value(PARAM_INT, 'Id do aluno no gestor'),
                        'firstname' => new external_value(PARAM_TEXT, 'Primeiro nome do aluno'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do aluno'),
                        'email' => new external_value(PARAM_TEXT, 'Email do aluno'),
                        'username' => new external_value(PARAM_TEXT, 'Usuario de acesso do aluno'),
                        'password' => new external_value(PARAM_TEXT, 'Senha do aluno'),
                        'city' => new external_value(PARAM_TEXT, 'Cidade do aluno')
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
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::update_student_parameters(), array('student' => $student));

        // Transforma o array em objeto.
        $student = (object)$student;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do usuario apartir do alu_id do aluno.
        $userid = self::find_user_by_alu_id($student->alu_id);

        // Dispara uma excessao se esse aluno ja estiver mapeado para um usuario.
        if(!$userid) {
            throw new Exception("Nenhum usuario esta mapeado para o aluno com alu_id: " . $student->alu_id);
        }

        $student->id = $userid;
        // Cria o usuario usando a biblioteca do proprio moodle.
        self::update_user($student);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        // Prepara o array de retorno.
        $returndata['id'] = $userid;
        $returndata['status'] = 'success';
        $returndata['message'] = 'Usuario atualizado com sucesso';

        return $returndata;
    }
    public static function update_student_parameters() {
        return new external_function_parameters(
            array(
                'student' => new external_single_structure(
                    array(
                        'alu_id' => new external_value(PARAM_INT, 'Id do aluno no gestor'),
                        'firstname' => new external_value(PARAM_TEXT, 'Primeiro nome do aluno'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do aluno'),
                        'email' => new external_value(PARAM_TEXT, 'Email do aluno'),
                        'city' => new external_value(PARAM_TEXT, 'Cidade do aluno')
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

    public static function create_teacher($teacher) {
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::create_teacher_parameters(), array('teacher' => $teacher));

        // Transforma o array em objeto.
        $teacher = (object)$teacher;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do usuario apartir do alu_id do aluno.
        $userid = self::find_user_by_prf_id($teacher->prf_id);

        // Dispara uma excessao se esse aluno ja estiver mapeado para um usuario.
        if($userid) {
            throw new Exception("Essa professor ja esta mapeado com o usuario de id: " . $userid);
        }
        
        // Cria o usuario usando a biblioteca do proprio moodle.
        $userid = self::save_teacher($teacher);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        // Prepara o array de retorno.
        if($userid) {
            $returndata['id'] = $userid;
            $returndata['status'] = 'success';
            $returndata['message'] = 'Usuario criado com sucesso';
        } else {
            $returndata['id'] = 0;
            $returndata['status'] = 'error';
            $returndata['message'] = 'Erro ao tentar criar o usuario';
        }

        return $returndata;
    }
    public static function create_teacher_parameters() {
        return new external_function_parameters(
            array(
                'teacher' => new external_single_structure(
                    array(
                        'prf_id' => new external_value(PARAM_INT, 'Id do professor no gestor'),
                        'firstname' => new external_value(PARAM_TEXT, 'Primeiro nome do professor'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do professor'),
                        'email' => new external_value(PARAM_TEXT, 'Email do professor'),
                        'username' => new external_value(PARAM_TEXT, 'Usuario de acesso do professor'),
                        'password' => new external_value(PARAM_TEXT, 'Senha do professor'),
                        'city' => new external_value(PARAM_TEXT, 'Cidade do professor')
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
        global $DB;

        //validate parameters
        $params = self::validate_parameters(self::update_teacher_parameters(), array('teacher' => $teacher));

        // Transforma o array em objeto.
        $teacher = (object)$teacher;

        // Inicia a transacao, qualquer erro que aconteca o rollback sera executado.
        $transaction = $DB->start_delegated_transaction();

        // Busca o id do usuario apartir do prf_id do aluno.
        $userid = self::find_user_by_prf_id($teacher->prf_id);

        // Dispara uma excessao se esse aluno ja estiver mapeado para um usuario.
        if(!$userid) {
            throw new Exception("Nenhum usuario esta mapeado para o professor com prf_id: " . $teacher->prf_id);
        }

        $teacher->id = $userid;
        // Cria o usuario usando a biblioteca do proprio moodle.
        self::update_user($teacher);

        // Persiste as operacoes em caso de sucesso.
        $transaction->allow_commit();

        // Prepara o array de retorno.
        $returndata['id'] = $userid;
        $returndata['status'] = 'success';
        $returndata['message'] = 'Usuario atualizado com sucesso';

        return $returndata;
    }
    public static function update_teacher_parameters() {
        return new external_function_parameters(
            array(
                'teacher' => new external_single_structure(
                    array(
                        'prf_id' => new external_value(PARAM_INT, 'Id do professor no gestor'),
                        'firstname' => new external_value(PARAM_TEXT, 'Primeiro nome do professor'),
                        'lastname' => new external_value(PARAM_TEXT, 'Ultimo nome do professor'),
                        'email' => new external_value(PARAM_TEXT, 'Email do professor'),
                        'city' => new external_value(PARAM_TEXT, 'Cidade do professor')
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
    protected static function find_user_by_alu_id($alu_id) {
        global $DB;
        
        // Busca o id do usuario apartir do alu_id do aluno.
        $sql = "SELECT userid FROM {itg_aluno_user} WHERE alu_id = :alu_id";
        $params['alu_id'] = $alu_id;
        $userid = current($DB->get_records_sql($sql, $params));

        if($userid) {
            $userid = $userid->userid;
        } else {
            $userid = 0;
        }

        return $userid;
    }
    protected static function find_user_by_prf_id($prf_id) {
        global $DB;
        
        // Busca o id do usuario apartir do prf_id do professor.
        $sql = "SELECT userid FROM {itg_professor_user} WHERE prf_id = :prf_id";
        $params['prf_id'] = $prf_id;
        $userid = current($DB->get_records_sql($sql, $params));

        if($userid) {
            $userid = $userid->userid;
        } else {
            $userid = 0;
        }

        return $userid;
    }
    protected static function save_student($student){
        global $DB;

        $userid = self::save_user($student);

        // Caso o curso tenha sido criado adiciona a tabela de controle os dados dos curso e da turma.
        $data['alu_id'] = $student->alu_id;
        $data['userid'] = $userid;

        $res = $DB->insert_record('itg_aluno_user', $data);
        
        return $userid;
    }
    protected static function save_teacher($teacher){
        global $DB;

        $userid = self::save_user($teacher);

        // Caso o curso tenha sido criado adiciona a tabela de controle os dados dos curso e da turma.
        $data['prf_id'] = $teacher->prf_id;
        $data['userid'] = $userid;

        $res = $DB->insert_record('itg_professor_user', $data);

        return $userid;
    }
    protected static function save_user($user) {
        global $CFG, $DB;

        // Inlcui a biblioteca de aluno do moodle
        require_once("{$CFG->dirroot}/user/lib.php");

        // Cria o curso usando a biblioteca do proprio moodle.
        $user->confirmed = 1;
        $user->mnethostid = 1;
        $userid = user_create_user($user);

        return $userid;
    }
    protected static function update_user($user) {
        global $CFG, $DB;

        // Inlcui a biblioteca de aluno do moodle
        require_once("{$CFG->dirroot}/user/lib.php");

        // Atualiza o curso usando a biblioteca do proprio moodle.
        user_update_user($user);
    }
}