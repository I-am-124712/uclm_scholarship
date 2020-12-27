<?php 
require './app/views/user_view.php'; 
require './app/core/ConstantsAndVars.php';

$recent = $args['recent'];
$count = count($recent);
?>

<div class="app-dash-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto" id="contents-panel">
        <div class="form-flat title">
            <b>MY SUMMARY REPORT</b>
        </div>
        <div class="form-flat">
            <div style="font-weight:bold; 
                        font-size: 20px;
                        color:rgb(30, 98, 223);">NEWLY RELEASED ALLOWANCE</div>
            <div class="notif-count-icon"><?= $count ?></div>
            <?php if($count > 0){?>
            <div class="form-flat" style="background:white; width:100%">
                <table class="table-flat">
                    <tr>
                        <th class="table-flat-header round-top-left">School Year</th>
                        <th class="table-flat-header">Period</th>
                        <th class="table-flat-header">Month</th>
                        <th class="table-flat-header">Gross Hours</th>
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
        <div class="form-flat">
            <div style="font-weight:bold; 
                        font-size: 20px;
                        color:rgb(30, 98, 223);">ALL SUMMARIES</div>
            <div class="form-flat" style="background:white; width:100%">
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
                        if(isset($args['summary'])){
                            foreach($args['summary'] as $summary) {

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
                        } 
                    
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="/uclm_scholarship/public/scripts/misc/realtime-update-util.js"></script>
<script>
    idForUpdate = 'contents-panel';
</script>