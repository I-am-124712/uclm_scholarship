<?php 

require 'summary_linker.html'; 

$departmentName = strtoupper($args['department']);
$data = $args['data'];

$monthNames = [
    'January', 
    'February', 
    'March', 
    'April', 
    'May', 
    'June', 
    'July', 
    'August', 
    'September', 
    'October', 
    'November',
    'December',
];

$departmentIndex = $data['departmentId'];
$schoolYear = $data['schoolYear'];
$schoolYears = explode('-', $schoolYear);
$period = $data['period'];
$month = $data['month'];

$monthName = $monthNames[$month];
$currentYear = $month >= 5 ? $schoolYears[0] : $schoolYears[1];

switch($period){
    case 1:
        $periodName = 'First';
        break;
    case 2:
        $periodName = 'Second';
}

$finder = $args['dbFinder'];

$sql = "SELECT WS.wsName, AllowanceSummary.gross_duty_hours, AllowanceSummary.total_late,
        AllowanceSummary.total_undertime, AllowanceSummary.overall_total
        FROM AllowanceSummary
        INNER JOIN WS on WS.idnumber = AllowanceSummary.ws_idnumber
        WHERE AllowanceSummary.ws_idnumber IN (SELECT idnumber from WS where depAssigned = ?)
        AND AllowanceSummary.school_year = ? AND AllowanceSummary.dtr_period = ?
        AND AllowanceSummary.dtr_month = ?;
";

$bindParams = [ $departmentIndex, $schoolYear, $period, $month ];

$res = $finder->ready()
->customSql($sql)
->setBindParams($bindParams)
->result_set();

$grandTotal = 0.0;
?>

<body>
    <div class="sheet">
        <div class="university">University of Cebu Lapu-lapu and Mandaue</div>
        <div class="university">A.C Cortes Avenue, Looc, Mandaue City, Philippines</div>
        <div class="period"><?= $periodName . " Period of " . $monthName . " " . $currentYear?></div>
        <table style="width:100%;">
            <caption class="deptCaption"><?= $departmentName ?></caption>
            <tr><b>
                <th class="name">Name</th>
                <th>Duty Hours</th>
                <th>Overtime</th>
                <th>Late</th>
                <th>Undertime</th>
                <th>Total Hours</th>
                <th>Signature</th>
            </b></tr>
            <?php foreach($res as $ws) {
                    $grandTotal += $ws->get('overall_total');
            ?>
            <tr>
                <td class="named"><?= utf8_encode($ws->get('wsName'))?></td>
                <td><?= $ws->get('gross_duty_hours')?></td>
                <td><?= $ws->get('total_late')?></td>
                <td><?= $ws->get('total_late')?></td>
                <td><?= $ws->get('total_undertime')?></td>
                <td><?= $ws->get('overall_total')?></td>
                <td></td>
            </tr>
            <?php } ?>
        </table>
        <br>
        <p><pre class="grandTotal">GRAND TOTAL : <?= $grandTotal ?>	</pre></p>
        <div class="assignatory">
            <div id="department-head" style="width:30%; float:left;">
                <div style="width:100%; text-align:center;">____________________________</div>
                <div style="width:100%; text-align:center;">Department Head</div>
            </div>
            <div id="scholarship-coordinator" style="width:30%; float:right; margin-right:1.5in">
                <div style="width:100%; text-align:center;">_____________________________</div>
                <div style="width:100%; text-align:center;">Scholarship Coordinator</div>
            </div>
        </div>
    </div>	
</body>