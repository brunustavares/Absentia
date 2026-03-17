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
 * @copyright  Copyright (C) 2019-present Bruno Tavares
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

if (isset($_POST['data_headr']) &&
    isset($_POST['data_array']) &&
    isset($_POST['file_name'])) { //caso as variáveis POST estejam preenchidas, o procedimento é executado

    $data_headr = unserialize(base64_decode($_POST['data_headr']));
    $data_array = unserialize(base64_decode($_POST['data_array']));
    $file_name = base64_decode($_POST['file_name']);
    $field_brk  = ';';

    $CSV_file   = header('Content-Type: text/csv; charset=UTF-8;');
    $CSV_file   = header('Content-Disposition:attachment; filename=' . $file_name . '.csv');
    $CSV_file   = fopen('php://output', 'w');

    fputcsv($CSV_file, $data_headr, $field_brk);

    foreach ($data_array as $data_rec) {
        fputcsv($CSV_file, $data_rec, $field_brk);

    }

    fclose($CSV_file);

}

?>
