
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


<div class="modal-overlay">
    <div class="modal-panel" id="panel-ws-info">    
        <?php
        if(isset($args['ws'])){
            $ws_name = explode(',',$args['ws']->get_fields()['wsName']);
            $lname = ltrim($ws_name[0]);
            $fname = ltrim($ws_name[1]);
        ?>
        <h1 class="modal-header"><b>WS Information</b></h1>
            <p><center class="label" id="dep-assigned">Department : 
                <uli class="label" id="label-deptname" style="font-size:24px">
                <b><?=isset($args['department'])? $args['department']:''?></b></uli></center></p>
        <div id="ws-info-form" style="padding-left:30px;padding-right:30px;padding-top:20px">
            <form action="" method="POST">

                <input type="hidden" name="selected-id" value="<?=$args['ws']->get_fields()['idnumber']?>">
                <?php 

                if($_SESSION['user_privilege']==999 || $_SESSION['user_privilege']==1)  // Only SysAdmin or Department Head allowed to edit ID Number
                    echo '<label id="form-label">ID Number <input spellcheck="false" class="textbox" name="idnumber" type="text" value='.$args['ws']->get_fields()['idnumber'].'></label>';
                else
                    echo '<label id="form-label" style="font-size:20px;height:60px">ID Number <uli class="label" id="label-deptname" style="font-size:24px"><b>'.$args['ws']->get_fields()['idnumber'].'</b></label><br>';
                ?>
                <?php 
                if($_SESSION['user_privilege']==999 || $_SESSION['user_privilege']==1 || $_SESSION['user_privilege']==2) 
                    echo '<label id="form-label">Last Name <input spellcheck="false" class="textbox" name="lname" type="text" value="'.utf8_encode($lname).'"></label>';
                else
                    echo '<label id="form-label" style="font-size:20px;height:60px">Last Name <uli class="label" id="label-deptname" style="font-size:24px"><b>'.utf8_encode($ws_name[0]).'</b></label><br>';
                ?>
                <?php 
                if($_SESSION['user_privilege']==999 || $_SESSION['user_privilege']==1 || $_SESSION['user_privilege']==2) 
                    echo '<label id="form-label">First Name <input spellcheck="false" class="textbox" name="fname" type="text" value="'.utf8_encode($fname).'"></label>';
                else
                    echo '<label id="form-label" style="font-size:20px;height:60px">First Name <uli class="label" id="label-deptname" style="font-size:24px"><b>'.utf8_encode($ws_name[1]).'</b></label><br>';
                ?>
                <?php 
                if($_SESSION['user_privilege']==999 || $_SESSION['user_privilege']==1 || $_SESSION['user_privilege']==2) 
                    echo '<label id="form-label">Date of Hire <input class="textbox" name="date_of_hire" type="date" value="'.date_format($args['ws']->get_fields()['dateOfHire'],'Y-m-d').'"></label>';
                else
                    echo '<label id="form-label" style="font-size:20px;height:60px">Date of Hire <uli class="label" id="label-deptname" style="font-size:24px"><b>'.date_format($args['ws']->get_fields()['dateOfHire'],'Y-m-d').'</b></label><br>';
                ?>
            </form>
        </div>
        <div class="container">
            <?php if($allowed_edit) {?>
            <button class="button-solid round" id="form-button" style="height: 40px" onclick="editWSInformation()">
                Edit</button>
            <button class="button-flashing round" id="form-button-transparent" style="height: 40px">
                View Schedule</button>
            <?php }?>
            <button class="button-flashing round" id="form-button-transparent" onclick="clearTargetHTML('for-popups')" style="height: 40px">
                Cancel</button>
        </div><br>
        <?php
        }
        ?>
    </div>
</div>