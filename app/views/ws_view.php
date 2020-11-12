
<?php
    $allowed_edit_user_detected = false;
    $allowed_edit_ajax_request = $args['allow_edit'];
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_privilege'])){
        if($_SESSION['user_privilege'] == 999 ||
            $_SESSION['user_privilege'] == 1 ||
            $_SESSION['user_privilege'] == 2) 
            $allowed_edit = true;
        if(isset($args['allow_edit']))
            $allowed_edit_user_detected = $args['allow_edit'];
    }
    else {
        echo "Trying to access data without logging in.";
        return;
    }

    require './app/views/user_view.php'; 
?>

<div class="app-dash-panel" id="dashboard-panel">
    <div> 
        <div>
            <button class="button-solid round" id="back-button" onclick="location.href='/uclm_scholarship/dash/departments'">Back to Previous</button><br>
        </div>
        <div class="form-flat" id="ws-table-panel" style="padding:20px; margin-top: 0; border-radius:20px">
            <span class="table-title" id="dept-name-title" style="width:100%">
                <b id="label-deptname"><?php echo strtoupper($this->model('Departments')->ready()->find()->where(['deptId' => $args['depAssigned']])->go()[0]->get_fields()['departmentName'])?></b>
            </span>
            <?php 
            if($allowed_edit_user_detected &&
                $allowed_edit_ajax_request){?>
                <button class="button-solid half-round" id="table-button-add" name="go" value="true" onclick="addWorkingScholars(<?=$args['depAssigned']?>)">Add Working Scholar</button>
            <?php }?>
            <table style="width:100%" id="ws-table">
                <tr>
                    <th class="table-flat-header clickable" onclick="tableSort('ws-table',0)">
                        <b>ID Number</b>
                    </th>
                    <th class="table-flat-header clickable" style="text-align:left" onclick="tableSort('ws-table',1)">
                        <b>WS Name</b>
                    </th>
                    <th class="table-flat-header round-top-right">
                        <?php if($allowed_edit_user_detected &&
                                $allowed_edit_ajax_request) { ?>
                            <b>Actions</b>
                        <?php } ?>
                    </th>
                </tr>
            <?php
            if(!empty($args['ws'])){
                foreach($args['ws'] as $ws){ ?>

                <tr>
                    <td class="table-flat-data" id="td-short"> <?=$ws->get_fields()['idnumber']//"▮▮▮▮▮▮▮▮" //?> </td>
                    <td class="table-flat-data" id="td-long" style="font-size:24px;text-align:left"><b><?= utf8_encode($ws->get_fields()['wsName'])?></b></td>
                    <td class="table-flat-data" id="td-max">
                    <?php if($allowed_edit_user_detected &&
                            $allowed_edit_ajax_request) {?>
                        <button class="button-solid round" id="action-button-info" value="<?=$ws->get_fields()['idnumber']?>" 
                        onclick="location.href = '/uclm_scholarship/dash/ws_information/'+this.value">View Info</button>
                        <button class="button-flashing round" id="action-button-delete" value="<?=$ws->get_fields()['idnumber']?>" onclick="deleteWorkingScholar(this.value)">Delete</button> 
                    </td>
                </tr>
            <?php   } }
            }
            ?>
            <tr>
                <td class="table-flat-footer" style="border-bottom-left-radius:20px"></td>
                <td class="table-flat-footer"></td>
                <td class="table-flat-footer" style="border-bottom-right-radius:20px"></td>
            </tr>
            </table>
        </div>
    </div>
</div>
<?php require './app/views/popups_view.php'; ?>

<script src="/uclm_scholarship/public/scripts/misc/ws-view-functions.js"></script>