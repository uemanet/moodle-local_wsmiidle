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
 * External Web Service Template
 *
 * @package    localwsmiidle
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_wsmiidle_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function hello_world_parameters() {
        return new external_function_parameters(
            array(
                'hello' => new external_single_structure(
                    array(
                        'message' => new external_value(PARAM_TEXT, 'Mensagem'),
                    )
                )
            )
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function hello_world($message) {
        // Valida os parametros.
        $params = self::validate_parameters(self::hello_world_parameters(), array('hello' => $message));

        $returndata['message'] = $message['message'] . ' no retorno';

        return $returndata;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function hello_world_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value(PARAM_TEXT, 'Mensagem de retorno da operacao')
            )
        );
    }
}