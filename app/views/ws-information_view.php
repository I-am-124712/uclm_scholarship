
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
?>


<?php require './app/views/user_view.php';  ?>
<div class="app-dash-panel">
    <div class="form-flat" id="info-form">
        <div id="ws-information">
            <div style="float:right;
                        color: green;
                        margin: 20px 20px 0px 0px"
                    id="edit-status">
                <?=Messages::dump('edit-status')?>
            </div>
            <div style="margin:10px">
                <form action="/uclm_scholarship/dash/ws?allow_edit" method="post">
                    <input hidden type="text" name="department" 
                            value=<?=$department_id?>>
                    <button class="button-solid round" id="back-button" type="submit">
                        Back to Previous
                    </button><br>
                </form>
            </div>
            <div style="margin:10px">
                <div class="photo-panel">
                    <div id="ws-photo" style="background-image: url('/uclm_scholarship/public/sources/users/user_default.png')"></div>
                </div>
                <div class="info-panel">
                    <ul class="info-words">
                        <li class="info-lines"><?="▮▮▮▮▮▮▮▮"//$args['ws']->get_fields()['idnumber']?></li>
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
                        <input hidden type="text" name="department" 
                                value=<?=isset($args['department'])? $args['department']->get_fields()['deptId']:''?>>
                        <label id="form-label2" style="color:black">
                            ID Number
                            <span style="color:red; font-size:10px; text-align:right; margin:0px 0px 0px 10px">
                                <?=Messages::dump('err_idnum')?>
                            </span>
                        </label>
                        <input type="text" id="selected-id" name="selected-id" value=<?=$idnumber?> hidden>
                        <input class="textbox-transparent" type="text" name="idnumber" value=<?="▮▮▮▮▮▮▮▮"//$idnumber?>>
                        <label id="form-label2" style="color:black">
                            Last Name
                        </label>
                        <input class="textbox-transparent" type="text" name="lname" value="<?=$lname?>">
                        <label id="form-label2" style="color:black">
                            First Name
                        </label>
                        <input class="textbox-transparent" type="text" name="fname" value="<?=$fname?>">
                        <label id="form-label2" style="color:black">
                            Date of Hire
                            <span style="color:red; font-size:10px; text-align:right; margin:0px 0px 0px 10px">
                                <?=Messages::dump('err_date')?>
                            </span>
                        </label>
                        <input class="textbox-transparent" type="date" name="date_of_hire" value=<?=$date_of_hire?>>
                        <label id="form-label2" style="color:black">
                            Course
                        </label>
                        <input class="textbox-transparent" type="text" name="course" value="<?=$course?>">
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
        <div style="margin-top:0px;padding-top:0px;text-align:center">
            <button id="save-edit" type="button" class="button-solid green">
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
                style="color:rgb(0,100,0);
                        float: right;
                        text-align: right;
                        width: auto;
                        height: auto;
                        margin-right: 10px;
                        font-size: 15px">
            </span>
        </div>
        <div class="tab-panel" id="sched-type" style="margin:0px 0px 0px 15px">
            <button class="button-tab" id="sched-type">Regular Days</button>
            <button class="button-tab" id="sched-type">Specific Day</button>
        </div>
        <div class="form-flat" id="sched-panel" style="margin-top:0px; width:100%">
            <div class="form-flat" style="margin-top:10px;padding:0px;border:0px;width:100%">
                <div class="form-flat" style="width: 100%">
                    <b><div id="day-label" style="margin-left:15px">SELECT DAYS</div></b>
                    <div class="form-flat" id="days-panel">
                        <button class="button-solid round-toggle" id="day-of-week" name="m" value="M">M</button>
                        <button class="button-solid round-toggle" id="day-of-week" name="tu" value="Tu">Tu</button>
                        <button class="button-solid round-toggle" id="day-of-week" name="w" value="W">W</button>
                        <button class="button-solid round-toggle" id="day-of-week" name="th" value="Th">Th</button>
                        <button class="button-solid round-toggle" id="day-of-week" name="f" value="F">F</button>
                        <button class="button-solid round-toggle" id="day-of-week" name="s" value="S">S</button>
                    </div>
                    <div class="form-flat" id="days-panel" style="display:auto; width:inherit; margin-left: auto; margin-right: auto">
                        <input type="date" name="spc-date" id="spc-date" class="textbox-transparent" 
                        style="float: none;
                                border: 0px;
                                margin-left:auto;
                                margin-right:auto;
                                width:calc(100%-30px)">
                    </div>
                </div>
                <form action="GET" style="width:450px;margin-top:0px;margin-left:auto;margin-right:auto">
                    <label for="tin" id="form-label2" style="color:black;width:80px">Time-In</label>
                    <input type="time" name="tin" id="tin" class="textbox-transparent" 
                    style="float:left;margin:5px;width:300px" value="08:00:00">
                    <label for="tout" id="form-label2" style="color:black;width:80px">Time-Out</label>
                    <input type="time" name="tout" id="tout" class="textbox-transparent" 
                    style="float:left;margin:5px;width:300px" value="09:00:00">
                </form>
                <button class="button-solid green" id="save-sched">Save Schedule</button>
            </div>
        </div>
        <div class="form-flat" style="width:100%;height:30px;padding:5px;margin:0px">
            <div style="margin:5px">
                <label for="semester">School Year</label>
            </div>
            <div style="margin:5px auto 5px auto; width:50%;">
                <select class="combo-dropbox" name="school-year" id="school-year">
                    <option value="2019-2020">2019-2020</option>
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
                                            id="action-button-info-icon" 
                                            value=<?=$scheduleId?>
                                            onclick="editSchedule($(this))"></button>
                                    <button class="button-flashing round" 
                                            id="action-button-delete-icon" 
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

<script type="text/javascript">

    const domParser = new DOMParser();
    const schedTypeNames = ["REG","SPC"];
    let domObj = null;
    let isEditScheduleMode = false;
    let schedTypeName = "REG";
    let selectedScheduleId = -1;



    // clears the selected Days toggle buttons. Used for initializing the page
    const clearDaysToggleButtons = _=>
    { 
        $(".form-flat#days-panel")
        .children("#day-of-week")
        .each(function(){
            $(this).removeClass("active");
        });
        $("#tin").val("08:00");
        $("#tout").val("09:00");
        $("#spc-date").val(null);
        selectedScheduleId = -1;
    }

    // Exit Edit Mode
    const exitEditMode = src =>{
        isEditScheduleMode = false;
        src.siblings(".button-tab").removeClass("active");
        src.addClass("active");
        $("#is-edit-mode").text("");
        clearDaysToggleButtons();
    }

    // loads the schedules in the Schedule panel
    const loadSched = function(){
        schedType = "schedType=" + schedTypeNames[$(".button-tab.active#sched-type").index()];
        semester = 'semester=' + ($(".button-tab.active#semester").index() + 1);
        schoolYear = $("#school-year").serialize();
        idnumber = $('#selected-id').serialize();
        args = schedType + "&" + semester + '&' + schoolYear + '&' + idnumber;
        url = "/uclm_scholarship/working_scholars/view_schedules";

        response = $.post({
            url : url,
            data: args,
            dataType: 'html',
            async: false
        }).responseText;


        domObj = domParser.parseFromString(response,'text/html');
        table = domObj.getElementById("sched-data");
    
        $("table#sched-data").replaceWith(table);
    };

    /* Edit Schedule */
    const editSchedule = ($src)=>{
        $(function(){
            // clean (reset) all Days toggle button first...
            clearDaysToggleButtons();


            if(!isEditScheduleMode){
                isEditScheduleMode = true;
            }
            $("#is-edit-mode").text("Edit Mode");
            scheduleId = $src.val();
            selectedScheduleId = scheduleId;

            // we will create a function for formatting our time string
            // to comply with the format required for the Time input fields.
            // Note that this is not usable for general cases of formatted
            // Time strings and only accepts Time strings with format "hh:mm am|pm".
            // Put short, just for the purpose of this functionality.
            //
            // Here's a smiley 😂
            const formatTime = (timeString)=>{
                let timeParts = timeString.replace(" ",":").split(':');
                let formattedString = '';
                for(let i=0; i<2; ++i){
                    timeParts[i] = parseInt(timeParts[i]);
                }
                let hour, minute;

                switch(timeParts[2]){
                    case "am":
                    case "AM":
                    case "aM":
                    case "Am":
                        hour = (timeParts[0] < 10) ? "0" + timeParts[0] : "" + timeParts[0];
                        minute = (timeParts[1] < 10) ? "0" + timeParts[1] : "" + timeParts[1];
                        break;
                    case "pm":
                    case "PM":
                    case "pM":
                    case "Pm":
                        hour = timeParts[0]==12? "" + 12 : "" + (12 + timeParts[0]);
                        minute = (timeParts[1] < 10) ? "0" + timeParts[1] : "" + timeParts[1];
                        break;
                }
                formattedString = hour + ":" + minute + ":00";

                return formattedString;
            }
            // Similar to the function above, we will make yet another local formatter
            // this time for the Date string. Just to comply with the date format required
            // by the HTML input element. Luckily this came out shorter than the time formatter.
            // Again, another smiley 😅
            const formatDate = dateString => {
                let dateParts = dateString.split("/");
                let month = (parseInt(dateParts[0]) < 10 ? "0"+dateParts[0]:dateParts[0]);
                let day = (parseInt(dateParts[1]) < 10 ? "0"+dateParts[1]:dateParts[1]);
                let year = dateParts[2];

                return year + "-" + month + "-" + day;
            }

            // we will then prepare our form for edit.
            // Start by extracting the Times-in and -out
            // from the calling Edit Button's parent row...
            let timeSched = $src.parents().find("div#time-" + scheduleId).text().trim().split(' - ');
            let timeIn = formatTime(timeSched[0]);
            let timeOut = formatTime(timeSched[1]);
            let schedDay = $src.parents().find("div#day-" + scheduleId).text().trim().split(' - ')[1];


            // then set the Time-in and Time-out fields with the selected value...
            $("input#tin").val(timeIn);
            $("input#tout").val(timeOut);

            switch(schedTypeName){
                case "REG": // For Regular Schedules
                    let days = schedDay.split(', ');

                    // activate all Days toggle button that matches the selected schedule's days...
                    if(days.length > 0){
                        for(let i=0; i<days.length; ++i){
                            $("#day-of-week[value=" + days[i] + "]").addClass("active");
                        }
                    }
                    break;
                case "SPC": // For Specific Schedules

                    // we simply have to update the Specific Date input field to
                    // the date of the selected schedule.
                    
                    $("input#spc-date").val(formatDate(schedDay));
                    break;
                default:
                    return;
            }
        });
    };


    /* Delete a selected Schedule */
    const deleteSchedule = (schedId)=>{
        scheduleId = "scheduleId="+schedId;
        $.ajax({
            url: '/uclm_scholarship/working_scholars/delete_schedule',
            type: 'post',
            data: scheduleId,
            success: function(res){
                console.log(res);
            }
        });
        loadSched();
    };

    /* confirmative Deletion function */
    const confirmDelete = schedId =>
        confirm("Are you sure you want to delete this Schedule?")? deleteSchedule(schedId):"";

    /* Switch the controls between WeekDay Selector and Date Picker */
    const scheduleType = ()=>{
        let label = ["SELECT DAYS","ENTER A SPECIFIC DATE"];
        source = $(".button-tab.active#sched-type");
        $("#day-label").text(label[source.index()]);
        $(".form-flat").children(".form-flat#days-panel").hide();
        $(".form-flat").children(".form-flat#days-panel").eq(source.index()).show();
    };


    // This area is for saving the schedule. We will find a better solution I swear.
    //
    // Edit as of November 3, 2020:
    //      This will be the main process for adding a schedule,
    // and will be used for editing it as well. Basically, an "Edit Schedule" action
    // is supposed to be an UPDATE SQL statement, instead it will be a combination 
    // of DELETE and INSERT. The reason being the aggregated schedule days for Regular 
    // Schedules sharing similar times-in and -out but with different Schedule Day(s)  
    // (eg. MWF 8AM-12PM, these schedules share the same sched_id and has to be deleted  
    // entirely because we shouldn't have two or more records that share the same  
    // sched_id but with different times-in and -out).
    //
    const saveSched = function(){
        
        $.ajax({
            type: 'GET',
            url: '/uclm_scholarship/working_scholars/schedule_index',
            dataType: 'JSON',
            success: function(data){
                // var schedTypeName = schedTypeNames[$(".button-tab.active#sched-type").index()];
                var schedType = "schedType=" + schedTypeName;
                var schoolYear = $("select#school-year").serialize();
                var semester = "semester=" + ($(".button-tab.active#semester").index()+1);
                var idnumber = $("#selected-id").serialize();
                var tin = $("input#tin").serialize();
                var tout = $("input#tout").serialize();
                var scheduleId = "schedule_id=" + (selectedScheduleId == -1? (data.schedId+1):selectedScheduleId);

                // We are in Edit mode? Delete the record first before saving.
                if(isEditScheduleMode)
                    deleteSchedule(selectedScheduleId);

                switch(schedTypeName){
                    // Regular Schedule
                    case "REG": 
                        // what we do is loop through every active day-of-week Element and add these schedules
                        $(".form-flat#days-panel")
                        .children("#day-of-week")
                        .each(function(){
                            if($(this).hasClass("active")){
                                schedDay = "schedDay=" + $(this).text();

                                params = scheduleId + "&"
                                        + schedType + "&"
                                        + schoolYear + "&"
                                        + semester + "&"
                                        + idnumber + "&"
                                        + schedDay + "&"
                                        + tin + "&"
                                        + tout;

                                response = $.post({
                                    url: '/uclm_scholarship/working_scholars/add_schedule',
                                    dataType: 'html',
                                    data: params,
                                    async: false
                                }).responseText;
                            }
                        });
                        loadSched();
                        break;

                    // Specific Schedule
                    case "SPC":
                        date = new Date($("input#spc-date").val());
                        schedDay = "schedDay=" + date.toLocaleDateString();

                        params = scheduleId + "&"
                                + schedType + "&"
                                + schoolYear + "&"
                                + semester + "&"
                                + idnumber + "&"
                                + schedDay + "&"
                                + tin + "&"
                                + tout;

                        response = $.post({
                            url: '/uclm_scholarship/working_scholars/add_schedule',
                            dataType: 'html',
                            data: params,
                            async: false
                        }).responseText;


                        loadSched();
                        break;
                    default:
                        
                }

                // Exit Edit mode if we are editing. We don't want to continually
                // edit the Schedule entry once we're done editing.
                _ = isEditScheduleMode? exitEditMode($(this)):'';

            },
            error: function(e){
                console.log(e.responseText);
            }
        });
    };  


    $(function(){

        /// Default selected tabs and items ///
        $("#sched-type").children(".button-tab").eq(0).addClass('active');
        $("#sched-sem").children(".button-tab").eq(0).addClass('active');
        
        /// load 1st Sem Schedule automatically
        scheduleType();
        loadSched();

        /// click a tab button. In this page, we make sure that the user
        /// will confirm changes when editing schedules before leaving...
        $(".button-tab").click(function(){
            if(isEditScheduleMode){
                if(confirm("Are you sure you want to leave Edit mode? Changes will not be saved.")){
                    exitEditMode($(this));
                }
            }
            else{
                $(this).siblings(".button-tab").removeClass("active");
                $(this).addClass("active");
            }
        });

        /// load records based on selected school year and semester;
        $(".button-tab#semester").click(function(){
            if(!isEditScheduleMode){
                loadSched();
            }
        });

        /// choose what type of schedule to save
        $(".button-tab#sched-type").click(function(){
            if(!isEditScheduleMode){
                schedTypeName = schedTypeNames[$(this).index()];
                $("#is-edit-mode").text("");
                scheduleType();
                loadSched();
            }
        });

        /// for Day of Week toggle buttons
        $(".button-solid.round-toggle#day-of-week").click(function(){
            _ = $(this).hasClass("active")? 
                        $(this).removeClass("active"):
                        $(this).addClass("active");
        });

        /// For WS Edit Information ///
        $("#save-edit").click(function(){
            let args = $("#information").serialize();

            // buffered page. we'll use this to query the edit form
            // and replace it in our current document without
            // reloading the page

            response = $.post({
                url: "/uclm_scholarship/working_scholars/update",
                data: args,
                dataType: 'html',
                async : false
            }).responseText;
            // console.log(response);

            domObj = domParser.parseFromString(response,'text/html');

            infoForm = domObj.getElementById("ws-information");


            $("div#ws-information").empty();
            $("div#ws-information").replaceWith(infoForm);
            $(this).text('Saved');
            $(this).delay(3000).fadeIn(500,function(){
                $(this).text('Edit Information');
            });
            $("#edit-status").delay(3000).fadeOut(500);

        });
        $('button#save-sched').click(saveSched);

    });
</script>