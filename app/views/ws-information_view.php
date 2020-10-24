
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
    $lname = explode(', ',$args['ws']->get_fields()['wsName'])[0];
    $fname = explode(', ',$args['ws']->get_fields()['wsName'])[1];
    $course = $args['ws']->get_fields()['course'];
    $date_of_hire = date_format($args['ws']->get_fields()['dateOfHire'],'Y-m-d');


?>


<?php require './app/views/user_view.php';  ?>

<div class="app-dash-panel">
    <div class="form-flat" id="info-form">
        <div style="float:right;
                    color: green;
                    margin: 20px 20px 0px 0px">
            <?=Messages::dump('edit-status')?>
        </div>
        <div style="margin:10px">
            <form action="/uclm_scholarship/dash/ws" method="post">
                <input hidden type="text" name="department" 
                        value=<?=isset($args['department'])? $args['department']->get_fields()['deptId']:''?>>
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
                    <li class="info-lines"><?=$args['ws']->get_fields()['idnumber']?></li>
                    <li class="info-lines"><?=$args['ws']->get_fields()['wsName']?></li>
                    <li class="info-lines"><?=strtoupper($args['department']->get_fields()['departmentName'])?></li>
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
                    <input type="text" name="selected-id" value=<?=$idnumber?> hidden>
                    <input class="textbox-transparent" type="text" name="idnumber" value=<?=$idnumber?>>
                    <label id="form-label2" style="color:black">
                        Last Name
                    </label>
                    <input class="textbox-transparent" type="text" name="lname" value=<?=$lname?>>
                    <label id="form-label2" style="color:black">
                        First Name
                    </label>
                    <input class="textbox-transparent" type="text" name="fname" value=<?=$fname?>>
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
                    <input class="textbox-transparent" type="text" name="course" value=<?=$course?>>
                    <div id="form-label2" style="color:black;padding-top:10px;margin-bottom:0px">
                        Assign as WS DTR In-Charge
                    </div>
                    <input type="checkbox" name="inCharge" value="true" style="margin-top: 20px;margin-bottom:0px">
                </form>
            </div>
        </div>
        <div style="margin-top:0px;padding-top:0px;text-align:center">
            <button id="save-edit" type="button" class="button-solid green">
                <?=$args['success'] ? 'Saved':'Edit Information'?>
            </button>
        </div>
    </div>
    <div class="form-flat" id="sched-form">
        <div style="color:rgb(255,115,0); text-align: left; width:100%; margin:10px 0px 10px 10px; font-size: 20px">
            <b>DUTY SCHEDULE</b>
        </div>
        <div class="tab-panel" style="margin:0px 0px 0px 15px">
            <button class="button-tab" active>Regular Days</button>
            <button class="button-tab">Specific Day</button>
        </div>
        <div class="form-flat" id="sched-panel" style="margin-top:0px; width:100%">
            <div class="form-flat" style="margin:none;border:0px;width:100%">
                Days
                <div class="form-flat" id="days-panel">
                    <button class="button-solid round toggle" id="m" onclick="highlight(this.id)">M</button>
                    <button class="button-solid round toggle" id="tu" onclick="highlight(this.id)">Tu</button>
                    <button class="button-solid round toggle" id="w" onclick="highlight(this.id)">W</button>
                    <button class="button-solid round toggle" id="th" onclick="highlight(this.id)">Th</button>
                    <button class="button-solid round toggle" id="f" onclick="highlight(this.id)">F</button>
                    <button class="button-solid round toggle" id="s" onclick="highlight(this.id)">S</button>
                </div>
                <form action="GET" style="width:450px;margin-top:20px;margin-left:auto;margin-right:auto">
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
                <select class="combo-dropbox" name="sy" id="sy">
                    <option value="2019-2020">2019-2020</option>
                </select>
            </div>
            <button class="button-solid round" 
            style="width:20%; float:right; margin-left:auto; margin-right:auto">
                Refresh
            </button>
        </div>
        <div class="tabbed-panel">
            <div class="tab-panel" style="margin: 0px 0px 0px 20px">
                <button class="button-tab">1st Semester</button>
                <button class="button-tab">2nd Semester</button>
                <button class="button-tab">Summer</button>
            </div>
            <div class="form-flat" style="margin: 0px 10px 10px 10px; height:150px; overflow-y:auto">
                <table class="table-flat">
                    <?php 
                    $days = ["MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURDAY"];
                    for($i=0; $i<10; ++$i) {?>
                        <tr>
                            <td class="table-flat-data transparent" id="schedule">
                                <div style="float:left;
                                            margin-top: auto;
                                            margin-bottom: auto;
                                            width:70%;">
                                    <div style="font-size:18px;">
                                        8:00 am - 12:00 pm
                                    </div>
                                    <div style="font-size:12px;color: rgb(50,50,255)">
                                        <?= $days[$i%6];?>
                                    </div>
                                </div>
                                <div style="width:15%; float:right; margin-bottom:5px">
                                    <button class="button-solid round" id="action-button-info-icon"></button>
                                    <button class="button-flashing round" id="action-button-delete-icon"></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let isActive = {
        "m": false,
        "tu": false,
        "w": false,
        "tu": false,
        "f": false,
        "s": false,
    };

    function highlight(id){
        isActive[id] = !isActive[id];
        console.log(id+" : "+isActive[id]);

        if(isActive[id]){
            document.getElementById(id).style.backgroundColor = "rgb(52, 133, 255)";
            document.getElementById(id).style.color = "white";
        }   
        else {
            document.getElementById(id).style.backgroundColor = "inherit";
            document.getElementById(id).style.color = "black";
        }
    }

    $(function(){
        /// FOR EDIT INFORMATION BUTTON ///
        var domObj;


        $("#save-edit").click(function(){
            let args = $("#information").serialize();
            let domParser = new DOMParser();

            // buffered page. we'll use this to query the edit form
            // and replace it in our current document without
            // reloading the page

            response = $.post({
                url: "/uclm_scholarship/working_scholars/update",
                data: args,
                dataType: 'html',
                async : false
            }).responseText;

            domObj = domParser.parseFromString(response,'text/html');

            infoForm = domObj.getElementById("info-form");
            $("#info-form").empty();
            $("#info-form").replaceWith(infoForm);
            $('#save-edit').delay(3000).fadeIn(500,function(){
                $(this).text('Edit Information');
            });
            // $('#save-edit').delay(3000);
        });
    });



</script>
<?php require './app/views/popups_view.php'; ?>