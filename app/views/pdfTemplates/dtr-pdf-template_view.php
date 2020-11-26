<?php require "dtr_linker.html"; 
    $wsName = isset($args["wsName"])? $args["wsName"]:"NONE";
?>

<body>
    <div class="sheet"> 
        <table class="table" align ="center" > 
            <tr class="tableHeader" > 
                <th id="dep" colspan ="10" >TEXTBOOK SECTION</th>
            </tr>
            <tr>
                <td colspan="9" style="font-size: 16px; padding: 5px 0px">
                    <?= $wsName ?>
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
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 05,2019</td>
                <td>08:00 AM</td>
                <td>12:00 PM</td>
                <td>07:40 AM</td>
                <td>12:09 PM</td>
                <td>4</td>
                <td>0</td>
                <td>0</td>
                <td>4</td>
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 06,2019</td>
                <td>08:00 AM</td>
                <td>12:00 PM</td>
                <td>07:44 AM</td>
                <td>12:31 PM</td>
                <td>4</td>
                <td>0</td>
                <td>0</td>
                <td>4</td>
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 07,2019</td>
                <td>03:00 PM</td>
                <td>06:00 PM</td>
                <td>01:43 PM</td>
                <td>06:01 PM</td>
                <td>3</td>
                <td>0</td>
                <td>0</td>
                <td>3</td>
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 09,2019</td>
                <td>08:00 AM</td>
                <td>04:30 PM</td>
                <td>07:52 AM</td>
                <td>12:49 PM</td>
                <td>5</td>
                <td>0</td>
                <td>3</td>
                <td>2</td>
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 11,2019</td>
                <td>08:00 AM</td>
                <td>12:00 PM</td>
                <td>07:14 AM</td>
                <td>12:47 PM</td>
                <td>4</td>
                <td>0</td>
                <td>0</td>
                <td>4</td>
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 12,2019</td>
                <td>08:00 AM</td>
                <td>12:00 PM</td>
                <td>07:39 AM</td>
                <td>01:33 PM</td>
                <td>4</td>
                <td>0</td>
                <td>0</td>
                <td>4</td>
            </tr>
            <tr>
                <!-- <td>Campugan, John Kenneth</td> -->
                <td>Dec 13,2019</td>
                <td>08:00 AM</td>
                <td>12:00 PM</td>
                <td>08:00 AM</td>
                <td>01:14 PM</td>
                <td>4</td>
                <td>0</td>
                <td>0</td>
                <td>4</td>
            </tr>
            <tr>
                <!-- <td></td> -->
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>28</td>
                <td>0</td>
                <td>3</td>
                <td>25</td>
            </tr>
        </table>
    </div>
</body>