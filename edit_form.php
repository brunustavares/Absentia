<?php
/**
 * Absentia
 * Moodle block to identify dropout-risk students and provide additional
 * contact info, via web service connected to the academic management system.
 * (developed for UAb - Universidade Aberta)
 *
 * @category   Moodle_Block
 * @package    block_absentia
 * @author     Bruno Tavares <brunustavares@gmail.com>
 * @link       https://www.linkedin.com/in/brunomastavares/
 * @copyright  Copyright (C) 2019-2025 Bruno Tavares
 * @license    GNU General Public License v3 or later
 *             https://www.gnu.org/licenses/gpl-3.0.html
 * @version    2021030107
 * @date       2019-08-22
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

class block_absentia_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        $mform->addElement('header', 'configheader', 'Calend&#xE1;rio Lectivo'); {

            $mform->addElement('text', 'config_calendar', 'Semestre');
            $mform->setDefault('config_calendar', '');
            $mform->setType('config_calendar', PARAM_RAW);

        }

        $mform->addElement('header', 'configheader', 'E-mail a enviar'); {

            $mform->addElement('text', 'config_subject', 'Assunto');
            $mform->setDefault('config_subject', '');
            $mform->setType('config_subject', PARAM_RAW);

            $mform->addElement('textarea',  'config_body', 'Corpo');
            $mform->setDefault('config_body', '');
            $mform->setType('config_body', PARAM_RAW);

        }

   }

} 

?>
