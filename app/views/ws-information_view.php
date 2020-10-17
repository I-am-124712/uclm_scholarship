
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
?>

<?php require './app/views/user_view.php';  ?>

<div class="app-dash-panel">
    <div class="form-flat">
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
                <form class="form-panel2" action="" method="">
                    <label id="form-label2" style="color:black">ID Number</label>
                    <input class="textbox-transparent" type="text" name="idnumber">
                    <label id="form-label2" style="color:black">Last Name</label>
                    <input class="textbox-transparent" type="text" name="lname">
                    <label id="form-label2" style="color:black">First Name</label>
                    <input class="textbox-transparent" type="text" name="fname">
                    <label id="form-label2" style="color:black">Date of Hire</label>
                    <input class="textbox-transparent" type="date" name="date_of_hire">
                    <label id="form-label2" style="color:black">Course</label>
                    <input class="textbox-transparent" type="text" name="course">
                </form>
            </div>
        </div>
        <div style="margin-top:0px;padding-top:0px">
            <button class="button-solid" id="form-button-green">Submit</button>
        </div>
    </div>
    <div class="form-flat" style="width:55%">

    </div>
</div>

<?php require './app/views/popups_view.php'; ?>