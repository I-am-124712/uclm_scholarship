<?php 
    $finder = $args['finder'];
    $months = [
        'JANUARY',
        'FEBRUARY',
        'MARCH',
        'APRIL',
        'MAY',
        'JUNE',
        'JULY',
        'AUGUST',
        'SEPTEMBER',
        'OCTOBER',
        'NOVEMBER',
        'DECEMBER'
    ];

    $maxDays = [31,29,31,30,31,30,31,31,30,31,30,31];


    $userId = $_SESSION['user_id'];

    $monthNum = (new DateTime())->format('m') + 0;
    $month = $months[$monthNum - 1];
    $maxDay = $maxDays[$monthNum - 1];
    $year = (new DateTime())->format('Y');
    $date = implode(" ", [$month, $year]);

    $dateStart = implode("-",[$monthNum, '01', $year ]);
    $dateEnd = implode("-",[$monthNum, $maxDay, $year ]);

    // Get our Working Scholar for the Department first
    $working = $finder->ready()
    ->customSql("SELECT WS.idnumber, WS.wsName, WS.dateOfHire,
        [User].user_photo FROM WS, [User] 
        where WS.user_id = [User].user_id
        AND depAssigned = (SELECT deptId from Departments where departmental_user_id = ?)")
    ->setBindParams([ $userId ])
    ->result_set();
    
    $res = [];

    foreach($working as $ws){
        $index = $ws->get('idnumber');
        $name = ($ws->get('wsName'));
        $userPhoto = $ws->get('user_photo');
        $dateHired = $ws->get('dateOfHire')->format('M d, Y');

        $res[$index] = [
            'wsName' => $name,
            'userPhoto' => $userPhoto,
            'dateHired' => $dateHired,
            'lates' => 0,
            'undertime' => 0,
            'incomplete' => 0
        ];
    }

    // We have to map their record summary to themselves since we would also want to see the
    // other working scholars that were assigned to the department.
    $summaryQuery = "SELECT sum(late) as 'lates',
        sum(undertime) as 'undertime',
        sum(case when timeIn is null or [timeOut] is null then 1 else 0 end) as 'incomplete'
        FROM Record WHERE idnumber = ? and recorddate between '$dateStart' and '$dateEnd'";

    foreach($res as $key => $value) {
        $idnumber = $key;
        $bindParam = [ $idnumber ];


        $res2 = $finder->ready()->customSql($summaryQuery)->setBindParams($bindParam)->result_set();

        if($res2 != null){
            $res2 = $res2[0];
            $lates = ($m = $res2->get('lates')) == null? 0 : $m; 
            $undertime = ($n = $res2->get('undertime')) == null? 0 : $n; 
            $incomplete = ($o = $res2->get('incomplete')) == null? 0 : $o; 
        }
        else{
            $lates = 0;
            $undertime = 0;
            $incomplete = 0;
        }
        $res[$key]['lates'] = $lates;
        $res[$key]['undertime'] = $undertime;
        $res[$key]['incomplete'] = $incomplete;
    }
?>


<div class="form-flat title-medium">
    <b>WORKING SCHOLARS' CURRENT PERFORMANCE SUMMARY: <span style="color:rgb(30, 98, 223)"><?= $date ?></span></b><br>
    <div class="form-flat" style="display:block; 
                                    flex-flow:none; 
                                    background:white; 
                                    width:auto; 
                                    height:220px; 
                                    overflow-x:auto; 
                                    overflow-y:hidden;
                                    padding:15px;">
        <div style="width:max-content; height:100%;">
            <?php 
                foreach($res as $key => $value) {
                    $idnumber = $key;
                    $wsName = utf8_encode($value['wsName']);
                    $dateHired = $value['dateHired'];
                    $lates = $value['lates'];
                    $undertime = $value['undertime'];
                    $incomplete = $value['incomplete'];
                    $user_photo = $value['userPhoto'];
            ?>
            <!-- WS Information stub -->
            <div class="form-flat block" style="width:400px; height:175px;">
                <!-- WS Credential -->
                <div style="width:auto; height:90px;">
                    <div style="float:left;
                                border-radius:100px; 
                                width:80px; 
                                height:80px; 
                                background:rgb(0, 64, 184);
                                margin: 5px;
                                background-size:cover;
                                background-repeat:no-repeat;
                                background-image:url('<?= $user_photo ?>')">
                    </div>
                    <div style="float:left; width:auto; margin:10px 15px;">
                        
                        <b><a style="float:left; font-size:14px" href=""><?=$idnumber?></a><br>
                        <span style="float:left; font-size:clamp(16px, 1vw, 18px);"><?=$wsName?></span></b><br>
                        <span style="float:left; font-size:12px; margin:5px 0px;">Date Hired: <?= $dateHired ?></span><br>
                        
                    </div>
                </div>
                <!-- WS Statistics -->
                <div style="width:auto; height:75px;">
                    <div style="width:auto; margin:10px 15px">
                        <span style="font-size:clamp(14px, 1vw, 16px)">Total Lates:</span>
                        <span style="font-size:16px; font-weight:bold; text-align:center; width:100%;"><?= $lates ?> Lates</span><br>
                        <span style="font-size:clamp(14px, 1vw, 16px)">Total Undertime:</span>
                        <span style="font-size:16px; font-weight:bold; text-align:center; width:100%;"><?= $undertime ?> Undertime</span><br>
                        <span style="font-size:clamp(14px, 1vw, 16px)">Incomplete Attendance:</span>
                        <span style="font-size:16px; font-weight:bold; text-align:center; width:100%;"><?= $incomplete ?> Found</span><br>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
</div>