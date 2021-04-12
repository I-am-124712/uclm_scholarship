<?php 

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

$departmentIndex = $data['departmentId'] + 0;
$schoolYear = ltrim(rtrim($args['schoolYear']));
$schoolYears = explode('-', $schoolYear);
$period = $args['period'] + 0;
$month = $args['month'] + 0;

$monthName = $monthNames[$month];
$currentYear = $month >= 5 ? $schoolYears[0] : $schoolYears[1];

switch($period){
    case 1:
        $periodName = 'First';
        break;
    case 2:
        $periodName = 'Second';
}


$selectSummary = "SELECT WS.idnumber, WS.wsName, AllowanceSummary.gross_duty_hours,
        AllowanceSummary.total_overtime, AllowanceSummary.total_late,
        AllowanceSummary.total_undertime, AllowanceSummary.overall_total 
        FROM AllowanceSummary 
        JOIN WS on WS.idnumber = AllowanceSummary.ws_idnumber 
        WHERE WS.depAssigned = ?
        AND AllowanceSummary.school_year = ? AND AllowanceSummary.dtr_period = ? 
        AND AllowanceSummary.dtr_month = ?
        ORDER BY WS.wsName";

$selectOvertime = "SELECT ";

$bindParams = [ $departmentIndex, $schoolYear, $period, $month ];

$conn = sqlsrv_connect('DESKTOP-LINBKQD\\UCLM_USO', ["Database" => "DTRDB2"]);
$query = sqlsrv_query($conn, $selectSummary, $bindParams);

$summaryResult = [];

while($result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
    array_push($summaryResult, $result);
}



sqlsrv_close($conn);

$grandTotal = 0.0;

$filename = "Summary $periodName Period of $monthName $currentYear - $departmentName";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/uclm_scholarship/public/styles/style_summary.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $filename ?></title>
</head>

<body onload="window.print()">
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
            <?php foreach($summaryResult as $ws) {
                    $grandTotal += $ws['overall_total'];
            ?>
            <tr>
                <td><span class="named"><?= utf8_encode($ws['wsName'])?></span></td>
                <td><?= $ws['gross_duty_hours']?></td>
                <td><?= $ws['total_overtime'] ?></td>
                <td><?= $ws['total_late']?></td>
                <td><?= $ws['total_undertime']?></td>
                <td><?= $ws['overall_total']?></td>
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
            <div id="scholarship-coordinator" style="width:30%; float:right; margin-right:0.5in">
                <div style="width:100%; text-align:center;">_____________________________</div>
                <div style="width:100%; text-align:center;">Scholarship Coordinator</div>
            </div>
        </div>
    </div>	
</body>