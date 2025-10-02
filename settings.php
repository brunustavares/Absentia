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

$settings->add(new admin_setting_heading(
        'Absentia_1S_Settings',
        'Calend&#xE1;rio Lectivo',
        '1&#xBA; Semestre')); {

        $settings->add(new admin_setting_configtext(
                'Absentia_1S_begin',
                'In&#xED;cio',
                'AAAA-MM-DD',
                '',
                PARAM_TEXT));

        $settings->add(new admin_setting_configtext(
                'Absentia_1S_end',
                'Fim',
                'AAAA-MM-DD',
                '',
                PARAM_TEXT));

}

$settings->add(new admin_setting_heading(
        'Absentia_2S_Settings',
        '',
        '2&#xBA; Semestre')); {

        $settings->add(new admin_setting_configtext(
                'Absentia_2S_begin',
                'In&#xED;cio',
                'AAAA-MM-DD',
                '',
                PARAM_TEXT));

        $settings->add(new admin_setting_configtext(
                'Absentia_2S_end',
                'Fim',
                'AAAA-MM-DD',
                '',
                PARAM_TEXT));

}

$settings->add(new admin_setting_heading(
        'Absentia_checkDelay_Settings',
        '',
        'In&#xED;cio de valida&#xE7;&#xE3;o')); {

        $arrDays = array('0 (ao 1º dia)', 1, 2, 3, 4, 5, 6, '7 (1 semana depois)');
        $settings->add(new admin_setting_configselect(
                'Absentia_checkDelay_Ndays',
                'N&#xFA;mero de dias',
                'desfasamento em rela&#xE7;&#xE3;o ao in&#xED;cio do semestre',
                0,
                $arrDays));
                
}               

$settings->add(new admin_setting_heading(
        'Absentia_BDIntSettings',
        'Base de Dados Interm&#xE9;dia (BDInt)',
        'Configura&#xE7;&#xE3;o do acesso &#xE0; BDInt')); {

        $settings->add(new admin_setting_configtext(
                'Absentia_BDhost',
                'Host',
                'servidor de base de dados',
                'localhost',
                PARAM_TEXT));

        $settings->add(new admin_setting_configtext(
                'Absentia_BDhost_Port',
                'Porta',
                'porta do servidor de base de dados',
                '3306',
                PARAM_INT));

        $settings->add(new admin_setting_configtext(
                'Absentia_BDInt',
                'Designa&#xE7;&#xE3;o',
                'nome da BDInt',
                'lead',
                PARAM_TEXT));

        $settings->add(new admin_setting_configtext(
                'Absentia_BDInt_usr',
                'Utilizador',
                'username da BDInt',
                '',
                PARAM_TEXT));

        $settings->add(new admin_setting_configpasswordunmask(
                'Absentia_BDInt_pwd',
                'Palavra-passe',
                'password da BDInt',
                '',
                PARAM_TEXT));
        
        $settings->add(new admin_setting_configtext(
                'Absentia_BDInt_WS',
                'Web Service',
                'URL do web service para consulta da BDInt',
                'http://mdlws.uab.pt/ws_BDInt/ws_BDInt.php',
                PARAM_URL));

}
                       
?>
