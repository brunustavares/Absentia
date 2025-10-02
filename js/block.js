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

// altura máxima da tabela
    $(document).ready(function () {
        $('#dtVScrollTbl').DataTable({
            'scrollY': '175px',
            'scrollCollapse': true,
            
        });

        $('.dataTables_length').addClass('bs-select');

    });

// refresh da página aquando da mudança de opção
    document.getElementById('lstacs_opt_15').onclick=function()
    {
        document.getElementById('lstacs_val').value='15';
        document.getElementById('frmAbsBlk').submit();

    }

    document.getElementById('lstacs_opt_30').onclick=function()
    {
        document.getElementById('lstacs_val').value='30';
        document.getElementById('frmAbsBlk').submit();

    }

    document.getElementById('lstacs_opt_ns').onclick=function()
    {
        document.getElementById('lstacs_val').value='ns';
        document.getElementById('frmAbsBlk').submit();

    }

// exportação p/ CSV
    function export_CSV() {
        document.getElementById('export_CSV').submit();

    }
