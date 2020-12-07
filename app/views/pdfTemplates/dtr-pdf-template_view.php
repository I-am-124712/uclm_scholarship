<?php require "dtr_linker.html"; 
    $department = str_replace('"', '', $args["department"]);
?>

<body>
    <div class="sheet"> 
        <table class="table" align="center" > 
            <tr class="tableHeader" > 
                <th id="dep" colspan ="10" ><?=strtoupper($department)?></th>
            </tr>
            <?php 
                if(isset($args['data'])){
                    foreach($args['data'] as $ws){
                        $wsName = strtoupper($ws['wsName']);

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
                            <td><?= $recordDate ?></td>
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
                            <td>'. $totalGrossHours .'</td>
                            <td>'. $totalLates .'</td>
                            <td>' . $totalUndertimes . '</td>
                            <td>' . $overallTotal . '</td>
                        </tr>';
                    }
                }
            ?>
        </table>
    </div>
</body>