<?php
    $department = ltrim(rtrim($args["department"]));

    $period = $args['period'];
    $month = $args['month'];
    $schoolYear = $args['schoolYear'];

    $filename = "DTR $period of $month $schoolYear - $department";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/uclm_scholarship/public/styles/style_dtr.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $filename ?></title>
</head>


<body onload="window.print()">
    <div id="dtr-sheet"> 
        <table class="table" style="margin:auto"> 
            <tr class="tableHeader" > 
                <th id="dep" colspan ="10" ><?=strtoupper($department)?></th>
            </tr>
            <?php 
                if(isset($args['data'])){
                    foreach($args['data'] as $ws){

                        if(empty($ws['wsRecords']))
                            continue;

                        $wsName = str_replace("ñ", 'Ñ', strtoupper($ws['wsName']));

                        $totalGrossHours = 0;
                        $totalLates = 0;
                        $totalUndertimes = 0;
                        $overallTotal = 0;

                        echo '
                        <tr>
                            <td colspan="9" style="font-size: 16px; padding: 5px 0px">
                                <b>'. $wsName .'</b>
                            </td>
                        </tr>
                        <tr>
                            <!-- <td class="name" rowspan ="2" > Name</td> -->
                            <td class="date" rowspan ="2" > Date</td>
                            <td class="sched" colspan ="2" > Schedules</td>
                            <td class="dt" colspan ="2" > Duty Hours</td>
                            <td rowspan="2" class="h" > Hours</td>
                            <td rowspan="2" class="l" > Late</td>
                            <td rowspan="2" class="u" > Undertime</td>
                            <td rowspan="2" class="t" > Total</td>
                        </tr>
                        <tr>
                            <td class="tintout" > T-IN</td>
                            <td class="tintout" > T-OUT</td>
                            <td class="tintout" > T-IN</td>
                            <td class="tintout" > T-OUT</td>
                        </tr>';
                        
                        foreach($ws['wsRecords'] as $record){
                            $recordDate = date_format(date_create($record['recorddate']['date']),"M d, Y");
                            $dayOfDate = $record['dayOfDate'];
                            $scheduleIn = $scheduleOut = "";
                            $grossHours = 0;

                            if(!empty($record['schedule']))
                                foreach($record['schedule'] as $schedule){
                                    $scheduleIn .= date_format(date_create($schedule['schedIn']['date']), "h:i A") . "<br>";
                                    $scheduleOut .= date_format(date_create($schedule['schedOut']['date']), "h:i A") . "<br>";
                                    $grossHours += $schedule['totalHours'];
                                }

                            $timeIn = isset($record['timeIn'])? 
                                    date_format(date_create($record['timeIn']['date']), "h:i A"): 
                                    '';
                            $timeOut = isset($record['timeOut'])?
                                    date_format(date_create($record['timeOut']['date']), "h:i A"): 
                                    '';
                            
                            $recordIn = $timeIn;
                            $recordOut = $timeOut;

                            $totalGrossHours += $grossHours;
                            $totalLates += $late = $record['late'];
                            $totalUndertimes += $undertime = $record['undertime'];
                            $overallTotal += $hoursRendered = $record['hoursRendered'];
                        ?>

                        <tr>
                            <td><?= "$recordDate ($dayOfDate)" ?></td>
                            <td><?= $scheduleIn ?></td>
                            <td><?= $scheduleOut ?></td>
                            <td><?= $recordIn ?></td>
                            <td><?= $recordOut ?></td>
                            <td><?= $grossHours ?></td>
                            <td><?= $late ?></td>
                            <td><?= $undertime ?></td>
                            <td><?= $hoursRendered ?></td>
                        </tr>
                        <?php 
                        }
                        echo '
                        <tr>
                            <td colspan="5" style="text-align: right; padding-right:10px"><b>TOTALS</b></td>
                            <td><b>'. $totalGrossHours .'</b></td>
                            <td><b>'. $totalLates .'</b></td>
                            <td><b>' . $totalUndertimes . '</b></td>
                            <td><b>' . $overallTotal . '</b></td>
                        </tr>';
                    }
                }
            ?>
        </table>
    </div>
</body>