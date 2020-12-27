
<?php
    $allowed_edit = false;
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_privilege'])){
        if($_SESSION['user_privilege'] == 999 ||
            $_SESSION['user_privilege'] == 1 ||
            $_SESSION['user_privilege'] == 2) 
            $allowed_edit = true;
    }
    else {
        echo "Trying to access data without logging in.";
        return;
    }

    $idnumber = $args['ws']->get_fields()['idnumber'];
    $lname = utf8_encode(explode(", ",$args['ws']->get_fields()['wsName'])[0]);
    $fname = utf8_encode(explode(", ",$args['ws']->get_fields()['wsName'])[1]);
    $course = $args['ws']->get_fields()['course'];
    $date_of_hire = date_format($args['ws']->get_fields()['dateOfHire'],'Y-m-d');
    $department_id = isset($args['department'])? $args['department']->get_fields()['deptId']:'';
    $department_name = strtoupper($args['department']->get_fields()['departmentName']);
    $user_privilege = $args['user']->get_fields()['user_privilege']+0;
    $user_photo = $args['user']->get('user_photo');
?>


<?php require './app/views/user_view.php';  ?>
<div class="app-dash-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat" style="width:auto;
                                    padding:20px;
                                    border-radius:20px;
                                    font-size:25px">
            <form id="back-to-previous" action="<?=isset($_SESSION['generalView'])? '/uclm_scholarship/dash/ws/general?allow_edit' : '/uclm_scholarship/dash/ws?allow_edit'?>" method="get">
                <input hidden type="text" name="department" value=<?=$department_id?>>
                <input hidden type="text" name="allow_edit" value="true">
                <button class="button-solid round" id="back-button" type="submit" style="margin-right:15px">
                    Back to WS List
                </button>
            </form>
            <b>WORKING SCHOLAR INFORMATION</b>
        </div>
        <div class="form-flat" id="info-form">
            <div id="ws-information">
                <div hidden style="float:right;
                            background-color: green;
                            color: white;
                            font-weight:bold;
                            border-radius:20px;
                            margin: 20px 20px 0px 0px;
                            padding:5px 10px;"
                        id="edit-status">
                    <?=Messages::dump('edit-status')?>
                </div>
                <div style="margin:10px">
                    <div class="photo-panel">
                        <div id="ws-photo" style="background-image: url('<?= $user_photo ?>')"></div>
                    </div>
                    <div class="info-panel">
                        <ul class="info-words">
                            <li class="info-lines"><?=$args['ws']->get_fields()['idnumber']?></li>
                            <li class="info-lines"><?=$lname.', '.$fname?></li>
                            <li class="info-lines"><?=$department_name?></li>
                        </ul>
                    </div>
                </div>
                <div class="edit-panel">
                    <div style="color:rgb(255,115,0);font-size:20px">
                        <b>EDIT INFORMATION</b>
                    </div>
                    <div class="form-panel2">
                        <form id="information" action="" method="">
                            <input hidden type="text" name="department" id="department"
                                    value=<?=$department_id?>>
                            <label id="form-label2" style="color:black">
                                ID Number
                                <span style="color:red; font-size:10px; text-align:right; margin:0px 0px 0px 10px">
                                    <?=Messages::dump('err_idnum')?>
                                </span>
                            </label>
                            <input type="text" id="selected-id" name="selected-id" value=<?=$idnumber?> hidden>
                            <input class="textbox" type="text" name="idnumber" value=<?=$idnumber?>>
                            <label id="form-label2" style="color:black">
                                Last Name
                            </label>
                            <input class="textbox" type="text" name="lname" value="<?=$lname?>">
                            <label id="form-label2" style="color:black">
                                First Name
                            </label>
                            <input class="textbox" type="text" name="fname" value="<?=$fname?>">
                            <?php if($_SESSION['user_privilege'] == 999 || $_SESSION['user_privilege'] == 1) {?>
                            <label id="form-label2" style="color:black">
                                Department
                            </label>
                            <select name="dep-assigned" id="dep-assigned" class="textbox" style="text-align-last:center; color:black; font-size:18px; height:auto">
                                <?php 
                                foreach($args['departmentsList'] as $departments) { 
                                    $deptId = $departments->get('deptId');
                                    $departmentName = $departments->get('departmentName');
                                    $selected = "";
                                    if($deptId == $department_id)
                                        $selected = "selected";
                                ?>
                                    <option value=<?= $deptId ?> <?= $selected ?>><?=$departmentName?></option>
                                <?php 
                                } ?>
                            </select>
                            <?php } ?>
                            <label id="form-label2" style="color:black">
                                Date of Hire
                                <span style="color:red; font-size:10px; text-align:right; margin:0px 0px 0px 10px">
                                    <?=Messages::dump('err_date')?>
                                </span>
                            </label>
                            <input class="textbox" type="date" name="date_of_hire" value=<?=$date_of_hire?>>
                            <label id="form-label2" style="color:black">
                                Course
                            </label>
                            <input class="textbox" type="text" name="course" value="<?=$course?>">
                            <?php if($_SESSION['user_privilege'] == 999 || $_SESSION['user_privilege'] == 1) { 
                                    if($department_name === "SCHOLARSHIP OFFICE"){
                                ?>
                                <div id="form-label2" style="color:black;padding-top:10px;margin-bottom:0px">
                                    Assign as WS DTR In-Charge
                                </div>
                                <input type="checkbox" name="inCharge" <?=($user_privilege===2)? 'checked':''?> style="margin-top: 20px;margin-bottom:0px">
                            <?php } 
                                }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <div style="margin:0px 0px 20px 0px;padding-top:0px;text-align:center">
                <button id="save-edit" type="button" class="button-solid blue">
                    Edit Information
                </button>
            </div>
        </div>
        <div class="form-flat" id="sched-form">
            <div id="sched-form-label" style="color:rgb(255,115,0);
                                            text-align: left;
                                            float: left;
                                            width:100%;
                                            margin:10px 0px 10px 10px;
                                            font-size: 20px">
                <b>DUTY SCHEDULE</b>
                <!-- For displaying a prompt saying user is currently in Edit Mode -->
                <span id="is-edit-mode" 
                    hidden
                    style=" border-radius:20px;
                            color:white;
                            background-color:rgb(0,100,0);
                            float: right;
                            font-weight:bold;
                            text-align: right;
                            width: auto;
                            height: auto;
                            margin-right: 10px;
                            padding:5px 10px;
                            font-size: 15px">
                            EDIT MODE
                </span>
            </div>
            <div class="tab-panel" id="sched-type" style="margin:0px 0px 0px 15px">
                <button class="button-tab" id="sched-type">Regular Days</button>
                <button class="button-tab" id="sched-type">Specific Day</button>
            </div>
            <div class="form-flat" id="sched-panel" style="margin-top:0px; width:100%">
                <div class="form-flat" style="margin-top:10px;
                                            padding:0px;
                                            border:0px;
                                            box-shadow: none;
                                            width:100%">
                    <div class="form-flat" style="width: 100%">
                        <b><div id="day-label" style="margin-left:15px">SELECT DAYS</div></b>
                        <div class="form-flat" id="days-panel" style="box-shadow: none">
                            <button class="button-solid round-toggle" id="day-of-week" name="m" value="M"><b>M</b></button>
                            <button class="button-solid round-toggle" id="day-of-week" name="tu" value="Tu"><b>Tu</b></button>
                            <button class="button-solid round-toggle" id="day-of-week" name="w" value="W"><b>W</b></button>
                            <button class="button-solid round-toggle" id="day-of-week" name="th" value="Th"><b>Th</b></button>
                            <button class="button-solid round-toggle" id="day-of-week" name="f" value="F"><b>F</b></button>
                            <button class="button-solid round-toggle" id="day-of-week" name="s" value="S"><b>S</b></button>
                        </div>
                        <div class="form-flat" id="days-panel" style="box-shadow: none; 
                                                                    display:auto; 
                                                                    width:inherit; 
                                                                    margin-left: auto; 
                                                                    margin-right: auto">
                            <input type="date" name="spc-date" id="spc-date" class="textbox-transparent" 
                            style="float: none;
                                    border: 0px;
                                    cursor: pointer;
                                    margin-left:auto;
                                    margin-right:auto;
                                    width:calc(100%-30px)">
                        </div>
                    </div>
                    <form action="GET" style="width:450px;margin-top:0px;margin-left:auto;margin-right:auto">
                        <label for="tin" id="form-label2" style="color:black;width:80px">Time-In</label>
                        <input type="time" name="tin" id="tin" class="textbox" 
                        style="float:left;margin:5px;width:300px" value="08:00:00">
                        <label for="tout" id="form-label2" style="color:black;width:80px">Time-Out</label>
                        <input type="time" name="tout" id="tout" class="textbox" 
                        style="float:left;margin:5px;width:300px" value="09:00:00">
                    </form>
                    <button class="button-solid blue" id="save-sched">Save Schedule</button>
                </div>
            </div>
            <div class="form-flat" style="width:100%;height:30px;padding:5px;margin:0px">
                <div style="margin:5px">
                    <label for="semester">School Year</label>
                </div>
                <div style="margin:5px auto 5px auto; width:50%;">
                    <select class="combo-dropbox" name="school-year" id="school-year" style="background-color:white">
                        <option value="2019-2020">2019-2020</option>
                        <option value="2020-2021">2020-2021</option>
                        <option value="2021-2022">2021-2022</option>
                        <option value="2022-2023">2022-2023</option>
                    </select>
                </div>
                <button class="button-solid round" 
                style="width:20%; float:right; margin-left:auto; margin-right:auto"
                onclick="loadSched()">
                    Refresh
                </button>
            </div>
            <div class="tabbed-panel">
                <div class="tab-panel" id="sched-sem" style="margin: 0px 0px 0px 20px">
                    <button class="button-tab" id="semester">1st Semester</button>
                    <button class="button-tab" id="semester">2nd Semester</button>
                    <button class="button-tab" id="semester">Summer</button>
                </div>
                <div class="form-flat" id="schedules" style="margin: 0px 10px 10px 10px; height:150px; overflow-y:auto">
                    <table class="table-flat" id="sched-data">
                        <!-- Table row here -->
                        <?php 
                        if(isset($args['schedule']) && !empty($args['schedule'])){
                            foreach($args['schedule'] as $sched) {
                                $scheduleId = $sched->get_fields()['schedule_id'];
                                $time_sched = date_format($sched->get_fields()['tin'],'h:i a')
                                            .' - '
                                            .date_format($sched->get_fields()['tout'],'h:i a');
                                $day_of_sched = $sched->get_fields()['schedDay'];
                                $total_hours = $sched->get_fields()['totalHours'];
                        ?>
                            <tr>
                                <td class="table-flat-data transparent" id="schedule">
                                    <div style="float:left;
                                                margin-top: auto;
                                                margin-bottom: auto;
                                                width:70%;">
                                        <div id="time-<?=$scheduleId?>" style="font-size:18px;" value=<?=$scheduleId?>>
                                            <?= $time_sched?>
                                        </div>
                                        <div id="day-<?=$scheduleId?>" style="font-size:12px;color: rgb(50,50,255)">
                                            <?= $total_hours.($total_hours > 1? ' Hours - ':' Hour - ').$day_of_sched?>
                                        </div>
                                    </div>
                                    <div style="float:right; margin-bottom:5px">
                                        <button class="button-solid round" 
                                                title="Edit"
                                                id="action-button-info-icon" 
                                                value=<?=$scheduleId?>
                                                onclick="editSchedule($(this))"></button>
                                        <button class="button-flashing round" 
                                                id="action-button-delete-icon" 
                                                title="Delete"
                                                value=<?=$scheduleId?>
                                                onclick="confirmDelete(this.value)"></button>
                                    </div>
                                </td>
                            </tr>
                        <?php } 
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/ws-information-functions.js"></script>