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

class block_absentia extends block_base {
    public $blockname = null;
    protected $contentgenerated = false;
    protected $docked = null;

    function init() {
        $this->blockname = get_class($this);
//        $this->title = 'Absentismo';
        $this->title = '';
        $this->content_type = BLOCK_TYPE_TEXT;
        
    }
    
    function instance_allow_config() {
        return true;
        
    }

    function has_config() {
        return true;
        
    }
  
    function applicable_formats() {
        return array('all' => true);
        
    }

    function instance_allow_multiple() {
        return false;
        
    }
    
    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        global $CFG, $DB, $COURSE, $USER;

        //verifica se o utilizador está devidamente autenticado e detém as permissões correctas
        $context = get_context_instance(CONTEXT_COURSE,$COURSE->id);

        if (!isloggedin()
            || !has_capability('moodle/course:update', $context)
        ) { //em caso negativo, não exibe qualquer conteúdo
            $this->content = null;

        //verifica se o código corresponde a um espaço ortodoxo (i.e. UC) ou MAO ou devL@b
        } elseif ((is_numeric(substr($COURSE->idnumber, 0, 5))
            && ((is_numeric(substr($COURSE->idnumber, -2)) && substr($COURSE->idnumber, -2) <> "00")
            || strtolower(substr($COURSE->idnumber, -6)) === 'matriz'))
            || (strtolower(substr($COURSE->idnumber, 0, 3)) === 'amb' && is_numeric(substr($COURSE->idnumber, 3, 5)))
            || $COURSE->idnumber === "devL@b"
        ) { //em caso afirmativo, constrói a interface

            echo "<link rel='stylesheet' type='text/css' href='../blocks/absentia/css/block.css' media='screen' />";

            // copyright
            $devCR = '<p style="font-size: 12px!important; text-align: right; margin-top: 0px!important; margin-bottom: 0px!important; padding-right: 5px!important;">
                        <a title="desenvolvido por..." href="http://www.linkedin.com/in/brunomastavares/" target = "_blank">&#xA9;2019</a>
                    </p>';

            date_default_timezone_set('Europe/London');
            $today = date("Y-m-d");
            
            //verifica se a UC está visível e decorrem actividades lectivas, de acordo com o calendário formal
            if (($COURSE->visible)
                && ((substr($COURSE->idnumber, 6, 2) >= substr($CFG->Absentia_1S_begin, 2, 2)
                //1º semestre
                && (($this->config->calendar === "1" && ($today >= $CFG->Absentia_1S_begin && $today <= $CFG->Absentia_1S_end))
                //2º semestre
                || ($this->config->calendar === "2" && ($today >= $CFG->Absentia_2S_begin && $today <= $CFG->Absentia_2S_end))
                //Anual
                || ($this->config->calendar === "A" && ($today >= $CFG->Absentia_1S_begin && $today <= $CFG->Absentia_2S_end))))
                //ambientação (MAO)
                || ((strtolower(substr($COURSE->idnumber, 0, 3)) === 'amb' && is_numeric(substr($COURSE->idnumber, 3, 5)))
                //Laboratório de Desenvolvimento
                || $COURSE->idnumber==="devL@b"))
            ) { //em caso afirmativo, apresenta informação

                $this->content = new stdClass;
        
                //configuração do estilo +
                //configuração dos objectos da interface
                $blkDta  = '<!DOCTYPE html>
                            <html>
                            <body>
                                <table align=center id="optTbl"><tr><td>
                                    <p style="text-align: center;margin-bottom: 0px!important;">
                                    <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 12.5vh;">
                                    </p></td><td>';

                                    /*Moodle v3.3
                                        <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 10vh;">
                                    */

                                    /*Moodle v3.6
                                        <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 12.5vh;">
                                    */

                //filtro para o período decorrido desde o último acesso
                if (!(strtolower(substr($COURSE->idnumber, 0, 3)) === 'amb'
                    && is_numeric(substr($COURSE->idnumber, 3, 5)))
                ) { //espaços fora do MAO
                    $check_start = '+' . $CFG->Absentia_checkDelay_Ndays . ' day'; //número de dias p/ desfasamento de validação de último acesso
            
                    $blkDta .=     '<p style="text-align: center;">Estudantes que n&#xE3;o acedem &#xE0;<br>Unidade Curricular:</p>
                                    <p style="margin-bottom: 0px!important;">';

                    //encerramento de forms abertas por erros de código alheios ao bloco, assegurando a abertura das necessárias ao seu funcionamento
                    $blkDta .= '</form></form></form></form></form></form></form></form></form></form>';

                    $blkDta .= '<form name="frmAbsBlk" id="frmAbsBlk" action="#" method="POST" enctype="multipart/form-data" style="padding-left:70px!important;">';

                    /*Moodle v3.3
                        $blkDta .= '<form name="frmAbsBlk" id="frmAbsBlk" action="#" method="POST" enctype="multipart/form-data" style="padding-left:30px!important;">';
                    */

                    /*Moodle v3.6
                        $blkDta .= '<form name="frmAbsBlk" id="frmAbsBlk" action="#" method="POST" enctype="multipart/form-data" style="padding-left:70px!important;">';
                    */

                    if (!isset($_POST['lstacs_val'])) { //opção por omissão
                        $blkDta .=  '<input type="radio" name="lstacs_opt" id="lstacs_opt_15" value="15" checked > +2 semanas<br>
                                     <input type="radio" name="lstacs_opt" id="lstacs_opt_30" value="30"         > +30 dias<br>
                                     <input type="radio" name="lstacs_opt" id="lstacs_opt_ns" value="ns"         > nunca';

                        $lstacs_dif = '15';

                    } else { //opção activa
                        if ($_POST['lstacs_val']==='15') {
                            $blkDta .= '<input type="radio" name="lstacs_opt" id="lstacs_opt_15" value="15" checked > +2 semanas<br>
                                        <input type="radio" name="lstacs_opt" id="lstacs_opt_30" value="30"         > +30 dias<br>
                                        <input type="radio" name="lstacs_opt" id="lstacs_opt_ns" value="ns"         > nunca';
            
                        } elseif ($_POST['lstacs_val']==='30') {
                            $blkDta .= '<input type="radio" name="lstacs_opt" id="lstacs_opt_15" value="15"         > +2 semanas<br>
                                        <input type="radio" name="lstacs_opt" id="lstacs_opt_30" value="30" checked > +30 dias<br>
                                        <input type="radio" name="lstacs_opt" id="lstacs_opt_ns" value="ns"         > nunca';
                            
                        } else {
                            $blkDta .= '<input type="radio" name="lstacs_opt" id="lstacs_opt_15" value="15"         > +2 semanas<br>
                                        <input type="radio" name="lstacs_opt" id="lstacs_opt_30" value="30"         > +30 dias<br>
                                        <input type="radio" name="lstacs_opt" id="lstacs_opt_ns" value="ns" checked > nunca';
                            
                        }
            
                        $lstacs_dif = $_POST['lstacs_val'];

                    }

                    $blkDta .=     '<input type="hidden" name="lstacs_val" id="lstacs_val" value="">
                                </form>';

                } else { //espaços do MAO
                    $check_start = '-21 day'; //número de dias de antecedência em relação ao início do semestre

                    $blkDta .=     '<p style="text-align: center;">
                                        Estudantes que ainda n&#xE3;o acederam ao M&#xF3;dulo de Ambienta&#xE7;&#xE3;o Online';
                                        
                    $lstacs_dif = 'ns';

                }

                //estabelece a data efectiva de início de validação, contemplando o desfasamento parametrizado para a leitura dos dados
                if ($this->config->calendar === "1"
                    || $this->config->calendar === "A"
                ) {
                    $startDt = new DateTime($CFG->Absentia_1S_begin);

                } else {
                    $startDt = new DateTime($CFG->Absentia_2S_begin);

                }

                $startDt = $startDt->modify($check_start)->format('Y-m-d');
                
                $blkDta .=     '</p></td></tr></table>';

                //consulta à BD Moodle
                $qryStr = 'SELECT usr.id, usr.username AS stdusr,
                                  CONCAT(
                                         SUBSTRING_INDEX(CONCAT( usr.firstname, " ", usr.lastname )," ",1),
                                         " " ,
                                         SUBSTRING_INDEX(CONCAT( usr.firstname, " ", usr.lastname )," ",(-1))
                                        ) AS stdshtname,
                                  CONCAT( usr.firstname, " ", usr.lastname ) AS stdfulname,
                                  usr.email AS email,
                                  FROM_UNIXTIME( usrlstacs.timeaccess, "%Y-%m-%d %H:%I" ) AS stdlstacs,
                                  crs.id AS crsid, crs.fullname AS crsfulname
                           FROM mdl_course crs
                                INNER JOIN mdl_context ctx ON (ctx.instanceid = crs.id AND ctx.contextlevel = 50)
                                INNER JOIN mdl_role_assignments rl ON (rl.contextid = ctx.id AND (rl.roleid = 5 OR rl.roleid = 28 OR rl.roleid = 17))
                                INNER JOIN mdl_user usr ON usr.id = rl.userid
                                INNER JOIN mdl_enrol enr ON enr.courseid = crs.id
								INNER JOIN mdl_user_enrolments ue ON (ue.userid = usr.id AND ue.enrolid = enr.id AND ue.status = 0)
                                LEFT JOIN mdl_user_lastaccess usrlstacs ON usrlstacs.userid = usr.id AND usrlstacs.courseid = crs.id
                           WHERE crs.id = ' . $COURSE->id . '
                                 AND crs.visible = 1
                           ORDER BY usr.username ASC;';

                $qryBDmdl = $DB->get_records_sql($qryStr);
                
                //formatação da tabela com Material Design for Bootstrap, para manter dimensão, viabilizar scroll/paginação e ordenação dos registos
                $stdTbl  = '<!-- MDBootstrap Datatables  -->
                                <link rel="stylesheet" type="text/css" href="../blocks/absentia/css/addons/datatables.min.css" />

                            <table align = center id="dtVScrollTbl" class="table table-striped table-bordered table-sm" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="th-sm" style="text-align: center;">nome</th>
                                        <th class="th-sm" style="text-align: center;">&#xFA;ltimo acesso</th>
                                    </tr>
                                </thead>
                                <tbody>';

                $mlLst = '';

                //verifica se a data actual cumpre o desfasamento estabelecido para a leitura dos dados
                if ($today >= $startDt) { //em caso afirmativo, efectua a leitura
                    $usrLst = '';

                    //concatena todos os usernames encontrados
                    foreach ($qryBDmdl as $id=>$stdMdl):
                        if (!empty($usrLst)) {
                            $usrLst .= ';' . $stdMdl->stdusr;

                        } else {
                            $usrLst .= $stdMdl->stdusr;

                        }

                    endforeach;

                    //consulta à BDInt, via web service
                    $dataFormat   = 'xml';
                    $accessMethod = 'get';
                    $warning = 'hidden';

                    if ($accessMethod==='post') {

                    } elseif ($accessMethod==='get') {
                        //verificação de disponibilidade da BDInt
                        try {
                            $stdLst = new SimpleXMLElement($CFG->Absentia_BDInt_WS
                                    .      '?BDhost='    . $CFG->Absentia_BDhost
                                    .      '&BDhostPrt=' . $CFG->Absentia_BDhost_Port
                                    .      '&BDInt='     . $CFG->Absentia_BDInt
                                    .      '&BDusr='     . $CFG->Absentia_BDInt_usr
                                    .      '&BDpwd='     . $CFG->Absentia_BDInt_pwd
                                    .      '&format='    . $dataFormat
                                    .      '&stdLst='    . $usrLst
                                    , 0, TRUE, '', FALSE);
                        
                        } catch (Exception $e) {
                            $warning = 'visible';

                        }

                    }

                    foreach ($stdLst as $stdInfo=>$std):
                        $stdBDint[] = array(
                                            'number'=>strval($std->number),
                                            'email'=>strval($std->email)
                                           );

                    endforeach;

                    foreach ($qryBDmdl as $id=>$stdMdl):
                        //calcula a diferença entre a data corrente e a data do último acesso, em número de dias
                        $howlong = date_diff(new DateTime(), date_create($stdMdl->stdlstacs))->format('%a');
    
                        if ($howlong >= $lstacs_dif
                            || empty($stdMdl->stdlstacs)
                        ) { //caso seja superior a 2 semanas ou vazia[=nunca], identifica o utilizador e a data do último acesso
                            $stdTbl .= '<tr>';
                                
                            //botão p/ envio de e-mail ao estudante: formatação
                            $stdCtcBtn = '<style> 
                                              #dtVScrollTbl { 
                                                  border-color: ' . $CFG->block_lanca_pautaOtherColor . '!important; 
                                              } 
                                              .absentia_btn:hover { 
                                                  color: '        . $CFG->block_lanca_pautaOtherColor . '!important; 
                                                  border-color: ' . $CFG->block_lanca_pautaOtherColor . '!important; 
                                              }
                                          </style>';
    
                            //dados p/ contacto
                            $email_pers = '';
                            $email_UAb  = $stdMdl->email;

                            if ($warning === 'hidden') { //caso haja conexão à BDInt
                                foreach ($stdBDint as $std):
                                    if ($stdMdl->stdusr === $std['number']
                                        && !empty($std['email'])
                                    ) { //caso exista registo do e-mail pessoal na BDInt, é utilizado
                                        $email_pers = $std['email'];
                                        break;

                                    } 
                                    
                                endforeach;

                            }

                            if (!empty($email_pers)) { //caso o e-mail pessoal esteja preenchido, é utilizado
                                $email_contact = $email_pers;

                            } else { //caso contrário, é utilizado o endereço registado no Moodle
                                $email_pers = '(sem registo)';
                                $email_contact = $email_UAb;

                            }

                            if (!empty($stdMdl->stdlstacs)) { //caso a data do último acesso esteja preenchida, é utilizada
                                $stdLstAcs = $stdMdl->stdlstacs;

                            } else { //caso contrário, significa que o utilizador nunca acedeu
                                $stdLstAcs = 'nunca';

                            }

                            //botão p/ envio de e-mail ao estudante: assunto, corpo e dados do destinatário    
                            if ($this->config->subject<>'') {
                                $email_subject = $this->config->subject;

                            } else {
                                $email_subject = 'absentismo na UC &#x22;' . $stdMdl->crsfulname . "&#x22;";

                            }
    
                            $email_body = 'nome: '               . $stdMdl->stdfulname . '%0D%0A'
                                        . 'e-mail pessoal: '     . $email_pers . '%0D%0A'
                                        . 'e-mail UAb: '         . $email_UAb . '%0D%0A'
                                        . '&#xFA;ltimo acesso: ' . $stdLstAcs . '%0D%0A';
    
                            if ($this->config->body<>'') {
                                $email_body .= '%0D%0A' . str_replace("\n", "%0D%0A", $this->config->body);

                            }
    
                            $stdCtcBtn .= '<a href="mailto:' . $email_contact
                                        .     '?subject='    . $email_subject
                                        .     '&body='       . $email_body
                                        . '" class="absentia_btn" title="' . $stdMdl->stdusr . '">' . $stdMdl->stdshtname . '</a>';
    
                            unset($stdLst);
    
                            $stdTbl .= '<td style="width: 50%; text-align: center;">' . $stdCtcBtn . '</td>
                                        <td style="width: 50%; text-align: center;">' . $stdLstAcs . '</td></tr>';
    
                            $tblData[] = array($stdMdl->stdfulname,$stdMdl->stdusr,$stdLstAcs,$email_pers,$email_UAb);
    
                            $mlLst .= $email_contact . ';';
    
                        }
    
                    endforeach;
        
                    //correcção do estado "warning" da BDInt, no caso de result set vazio
                    if (!$tblData) {
                        $warning = 'hidden';

                    }

                    $stdTbl .=         '</tbody>
                                    </table>';

                    if ($this->config->body<>'') {
                        $email_body = str_replace("\n", "%0D%0A", $this->config->body);

                    } else {
                        $email_body = '';

                    }

		    if ($tblData) {
                        //botão p/ envio de e-mail ao estudante: dados para notificação global
                        if (count($tblData) >= 2) {
                            $mlLstCtc = 'mailto:'
                                      . '?bcc='     . $mlLst
                                      . '&subject=' . $email_subject
                                      . '&body='    . $email_body;

                            $mlLstIco = '<a href="' . $mlLstCtc . '">
                                             <img src="../blocks/absentia/img/mailing_list/ML_01.png" alt="contact_All" title="contactar todos" height="30"
                                                 onmouseout="this.src=\'../blocks/absentia/img/mailing_list/ML_01.png\';"
                                                 onmouseover="this.src=\'../blocks/absentia/img/mailing_list/ML_02.png\';">
                                         </a>';

                        } else {
                            $mlLstIco = '<img src="../blocks/absentia/img/mailing_list/ML_03.png" alt="contact_All" 
                                             title="contactar todos:&#10;&#13;dispon&#xED;vel apenas quando&#10;&#13;h&#xE1; m&#xFA;ltiplos registos" 
                                             height="30">';

                        }

                        //dados de exportação p/ CSV
                        if (count($tblData) >= 1) {
                            $tblHedr = base64_encode(serialize(array('nome','num','ult_acesso','email_pess','email_UAb')));
                            $tblData = base64_encode(serialize($tblData));

                            $file_name = base64_encode('Absentia'
                                       .               '_UC' . $COURSE->idnumber
                                       .               '_d'  . date("Ymd")
                                       .               '_t'  . date("Hi"));

                            $XLSIco = '<a href="javascript:export_CSV()">
                                           <img src="../blocks/absentia/img/csv/CSV_01.png" alt="export_CSV" title="exportar p/ CSV" height="30"
                                               onmouseout="this.src=\'../blocks/absentia/img/csv/CSV_01.png\';"
                                               onmouseover="this.src=\'../blocks/absentia/img/csv/CSV_02.png\';">
                                       </a>';
    			}

                    } else {
                        $XLSIco = '<img src="../blocks/absentia/img/csv/CSV_03.png" alt="contact_All" 
                                       title="exportar p/ CSV:&#10;&#13;dispon&#xED;vel apenas&#10;&#13;quando h&#xE1; registos" 
                                       height="30">';

                    }

                    $blkDta .= $stdTbl
                            . '<hr>
                               <form name="export_CSV" id="export_CSV" action="../blocks/absentia/export_CSV.php" method="POST" enctype="multipart/form-data">
                                   <input type="hidden" name="data_headr" value=' . $tblHedr . '/>
                                   <input type="hidden" name="data_array" value=' . $tblData . '/>
                                   <input type="hidden" name="file_name" value='  . $file_name . '/>
                               </form>
                               <table align=center style="width: 100%;"><tr>
                                   <td style="width: 20%;text-align: left;">
                                       <img src="../blocks/absentia/img/warning.png" alt="BDInt_warning" 
                                           title="base de dados de e-mails pessoais&#10;&#13;indispon&#xED;vel, sendo utilizados&#10;&#13;aqueles que se encontram&#10;&#13;registados na PlataformAbERTA" 
                                           height="30" style="visibility:' . $warning . ';"></td>
                                   <td style="width: 60%;text-align: center;">'. $mlLstIco . $XLSIco . '</td>
                                   <td style="width: 20%;text-align: right;">' . $devCR . '</td></tr>
                               </table>';
                    
                    // scripts a executar nos requests web
                    $blkDta .= "<!-- MDBootstrap Datatables  -->
                                    <!-- JQuery
                                        <script type='text/javascript' src='../blocks/absentia/js/jquery-3.4.1.min.js'></script> -->

                                    <!-- Bootstrap core JavaScript
                                        <script type='text/javascript' src='../blocks/absentia/js/bootstrap.min.js'></script> -->

                                    <!-- Bootstrap tooltips
                                        <script type='text/javascript' src='../blocks/absentia/js/popper.min.js'></script> -->

                                    <!-- MDB core JavaScript -->
                                        <script type='text/javascript' src='../blocks/absentia/js/mdb.min.js'></script>

                                    <!-- MDBootstrap Datatables  -->
                                        <script type='text/javascript' src='../blocks/absentia/js/addons/datatables.min.js'></script>

                                <!-- JavaScript do bloco  -->
                                    <script type='text/javascript' src='../blocks/absentia/js/block.js'></script>

                                </body></html>";

                } else { //em caso negativo, apresenta informação sobre o desfasamento
                    $blkDta = '<table align=center id="optTbl"><tr><td>
                                   <p style="text-align: center; margin-bottom: 0px!important;"><br>
                                   <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 12.5vh;">
                                   </p></td><td style="text-align: center; margin-bottom: 0px!important; background-image: url(../blocks/absentia/img/info/info.png); background-repeat: no-repeat; background-size: 110px!important; background-position: center;">
                                   <p><br><br>
                                       A valida&#xE7;&#xE3;o de absentismo ser&#xE1; iniciada dentro de ' . date_diff(new DateTime('today'), date_create($startDt))->format('%a') . ' dia(s)
                                   <br><br></p></td></tr>
                               </table>'
                            .  $devCR;

                               /*Moodle v3.3
                                   <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 10vh;">
                               */

                               /*Moodle v3.6
                                   <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 12.5vh;">
                               */

                }

                $this->content->text = $blkDta;

            } else { //em caso negativo, não apresenta informação
                $this->content->text = '<table align=center id="optTbl"><tr><td>
                                            <p style="text-align: center; margin-bottom: 0px!important;"><br>
                                            <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 12.5vh;">
                                            </p></td><td style="text-align: center; color: #c55!important; margin-bottom: 0px!important; background-image: url(../blocks/absentia/img/info/warning.png); background-repeat: no-repeat; background-size: 120px!important; background-position: center;">
                                            <p><br>
                                                Informa&#xE7;&#xE3;o dispon&#xED;vel estando a UC aberta e dentro do per&#xED;odo estabelecido no calend&#xE1;rio lectivo
                                            </p></td></tr>
                                        </table>'
                                     .  $devCR;

                                        /*Moodle v3.3
                                            <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 10vh;">
                                        */

                                        /*Moodle v3.6
                                            <img src="../blocks/absentia/img/absentia.png" alt="logo_Absentia" title="Absentia v1" class="responsive" style="height: 12.5vh;">
                                        */

            }

        } else { //em caso negativo, não exibe qualquer conteúdo
            $this->content = null;

        }
                                
        return $this->content;
                      
    }
      
}
    
?>
