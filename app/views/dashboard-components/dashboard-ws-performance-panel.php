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

    $userId = $_SESSION['user_id'];

    $sql = "SELECT WS.idnumber, WS.wsName,
        WS.dateOfHire, [User].user_photo,
        sum(late) as 'lates', 
        sum(undertime) as 'undertime', 
        sum(case when timein is null or [timeOut] is null then 1 else 0 end) as 'incomplete' from Record
        inner join WS on WS.idnumber = Record.idnumber
        inner join [User] on [User].user_id = WS.user_id
        where WS.depAssigned = (SELECT deptId FROM Departments where departmental_user_id = ?)
        and recorddate between '11-01-2019' and '11-30-2019'
        group by WS.idnumber, WS.wsName, WS.dateOfHire, [User].user_photo
        order by sum(hoursRendered) DESC";

    $bindParams = [ $userId ];
    
    $month = $months[(new DateTime())->format('m') + -1];
    $year = (new DateTime())->format('Y');
    $date = implode(" ", [$month, $year]);

    $res = $finder->ready()->customSql($sql)->setBindParams($bindParams)->result_set();
?>


<div class="form-flat title-medium">
    <b>WORKING SCHOLARS PERFORMANCE SUMMARY: <span style="color:rgb(30, 98, 223)"><?= $date ?></span></b><br>
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
                foreach($res as $ws) {
                    $idnumber = $ws->get('idnumber');
                    $wsName = utf8_encode($ws->get('wsName'));
                    $dateHired = $ws->get('dateOfHire')->format('M d, Y');
                    $lates = $ws->get('lates');
                    $undertime = $ws->get('undertime');
                    $incomplete = $ws->get('incomplete');
                    $user_photo = $ws->get('user_photo');
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