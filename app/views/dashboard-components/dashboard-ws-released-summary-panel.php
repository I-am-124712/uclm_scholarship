<?php 
require './app/core/ConstantsAndVars.php';

$finder = $args['finder'];

$idnumber = str_replace('ws', '', $_SESSION['user_id']);

$sql = 'SELECT * FROM AllowanceSummary where ws_idnumber = ? 
AND CURRENT_TIMESTAMP - update_timestamp <= (60*60*24*31)
AND allowance_status = \'RELEASED\'';

$bindParam = [ $idnumber ];


$recent = $finder->ready()
->customSql($sql)
->setBindParams($bindParam)
->result_set();

$count = count($recent);

?>
<div class="form-flat">
    <div id="for-released-summary">
        <div style="font-weight:bold;
                    float:left;
                    font-size: 20px;
                    color:rgb(30, 98, 223);">
                    NEWLY RELEASED ALLOWANCE SUMMARIES
        </div>
        <div class="notif-count-icon"><?= $count ?></div>
        <?php if($count > 0){?>
        <div class="form-flat" style="float:left; width:auto; background:white">
            <table class="table-flat">
                <tr>
                    <th class="table-flat-header round-top-left">School Year</th>
                    <th class="table-flat-header">Period</th>
                    <th class="table-flat-header">Month</th>
                    <th class="table-flat-header">Duty Hours</th>
                    <th class="table-flat-header">Lates</th>
                    <th class="table-flat-header">Undertime</th>
                    <th class="table-flat-header">Total Hours</th>
                    <th class="table-flat-header">To Peso</th>
                    <th class="table-flat-header round-top-right">Status</th>
                </tr>
                <?php 
                    foreach($recent as $summary){

                        $schoolYear = $summary->get('school_year');
                        switch($summary->get('dtr_period')){
                            case 1:
                                $period = 'First Period';
                                break;
                            case 2:
                                $period = 'Second Period';
                                break;
                        }
                        $month = $months[$summary->get('dtr_month')];
                        $gross = $summary->get('gross_duty_hours');
                        $lates = $summary->get('total_late');
                        $undertime = $summary->get('total_undertime');
                        $totalHours = $summary->get('overall_total');
                        $toPeso = number_format($totalHours * 10.00,2,'.',',');
                        $status = $summary->get('allowance_status');
                        switch($status){
                            case 'UNRELEASED':
                                $color = "red";
                                break;
                            case 'RELEASED':
                                $color = "green";
                        }
                ?>
                <tr>
                    <td class="table-flat-data"><?= $schoolYear ?></td>
                    <td class="table-flat-data"><?= $period ?></td>
                    <td class="table-flat-data"><?= $month ?></td>
                    <td class="table-flat-data"><?= $gross ?></td>
                    <td class="table-flat-data"><?= $lates ?></td>
                    <td class="table-flat-data"><?= $undertime ?></td>
                    <td class="table-flat-data"><?= $totalHours ?></td>
                    <td class="table-flat-data"><?= "P " . $toPeso ?></td>
                    <td class="table-flat-data">
                        <div style="font-weight:bold; 
                                    border-radius:20px;
                                    padding:5px 0px; 
                                    background:<?=$color?>"><?= $status ?></div>
                    </td>
                </tr>
                <?php 
                    } 
                ?>
            </table>
        </div>
        <?php }?>
    </div>
</div>