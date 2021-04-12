<?php

require 'user_view.php';

$departments = $args['departments'];
$workingScholars = isset($args['ws']) ? $args['ws']: [];
$savedOvertime = isset($args['savedOvertime'])? $args['savedOvertime']:[]; 
$selectedDepartment = isset($args['selectedDepartment']) ? $args['selectedDepartment']:-1;
$selectedID = isset($args['selectedID']) ? $args['selectedID']:-1;
$wsInfo = isset($args['ws-info']) ? $args['ws-info'] : [];

?>

<div class="app-dash-panel">
    <div class="form-flat title"><b>ENCODE OVERTIME</b></div>
    <div class="form-flat">
        <!-- Selector input field panel -->
        <div style="width:100%;">
            <form action="/uclm_scholarship/records/overtime/encode" method="get" style="width:100%; padding:10px 0px">
                    <span for="department" style="width:200px">Department
                    </label>
                    <select name="department" id="department" class="textbox" 
                    style="font-size:0.9vw; height:30px; padding:5px 10px; background:white; width:250px; float:unset">
                    <?php foreach($departments as $dep) {
                            $deptId = $dep->get('deptId');
                            $departmentName = $dep->get('departmentName');
                            $selected = ($deptId == $selectedDepartment) ? 'selected' : '';
                    ?>
                        <option value=<?= $deptId ?> <?= $selected ?>><?= $departmentName ?></option>
                    <?php } ?>
                    </select>
                    <button type="submit" class="button-solid round" id="btn-load"
                            style="float:right; 
                                    background-color:rgb(43, 117, 255); 
                                    font-size:16px;">Load
                    </button>
            </form>
            <label for="working-scholar">Working Scholar
                <select name="working-scholar" id="working-scholar" class="textbox" 
                style="font-size:0.9vw; height:30px; padding:5px 10px; background:white; width:250px; float:unset">
                    <?php 
                    if(!empty($workingScholars)) { 
                        foreach($workingScholars as $ws) {
                            $idnumber = $ws->get('idnumber');
                            $wsName = utf8_encode($ws->get('wsName'));
                            $selected = ($idnumber == $selectedID) ? 'selected' : '';
                    ?>
                        <option value=<?= $idnumber ?> <?= $selected ?>><?= $wsName ?></option>
                    <?php }
                    } 
                    else { ?>
                    <option value="none">-- No WS Loaded --</option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div style="width:100%; height:80px; margin:10px">
        <?php 
            if(!empty($wsInfo)){
                $idnumber = $wsInfo->get('idnumber');
                $wsName = utf8_encode($wsInfo->get('wsName'));
        ?>
            ID Number: <span id="idnumber" style="color:rgb(0, 81, 255); font-size:20px; font-weight:bold"><?= $idnumber ?></span><br>
            WS Name: <span style="color:rgb(0, 81, 255); font-size:20px; font-weight:bold"><?= $wsName ?></span>
        <?php 
            }else {
        ?>
            ID Number: <span style="color:rgb(0, 81, 255); font-size:20px; font-weight:bold">None Selected</span><br>
            WS Name: <span style="color:rgb(0, 81, 255); font-size:20px; font-weight:bold"></span>
        <?php } ?>
        </div>
        <!-- For Saved Overtime table -->
        <label for="" id="form-label2" style="font-size:20px"><b>SAVED OVERTIME</b></label>
        <table class="table-flat">
            <tr>
                <th class="table-flat-header round-top-left">OVERTIME DATE</th>
                <th class="table-flat-header">START TIME</th>
                <th class="table-flat-header">END TIME</th>
                <th class="table-flat-header">TOTAL RENDERED TIME</th>
                <th class="table-flat-header round-top-right">ACTIONS</th>
            </tr>
        </table>
        <div style="width:100%; overflow-y:auto; 
                max-height:200px; 
                border-bottom-left-radius:20px;
                border-bottom-right-radius:20px;">
            <table class="table-flat">
                <?php 
                    if(!empty($savedOvertime)) {
                        $len = count($savedOvertime);
                        $i = 0;

                        foreach($savedOvertime as $ot) {
                            $i++;
                            $otid = $ot->get('overtime_id');
                            $otdate = date_format($ot->get('otdate'), 'M d, Y');
                            $ottimestart = date_format($ot->get('ottimestart'), 'h:i A');
                            $ottimeend = date_format($ot->get('ottimeend'), 'h:i A');
                            $ottotal = $ot->get('ottotal');
                            
                            if($i == $len) {
                                $left = 'style="width:20%;border-bottom-left-radius:20px"';
                                $right = 'style="width:15%;border-bottom-right-radius:20px"';
                            }
                            else
                                $left = $right = '';
                ?>
                <tr id=<?= $otid ?>>
                    <td class="table-flat-data" <?= $left ?>><?= $otdate ?></td>
                    <td class="table-flat-data" style="width:20%"><?= $ottimestart ?></td>
                    <td class="table-flat-data" style="width:20%"><?= $ottimeend ?></td>
                    <td class="table-flat-data" style="width:25%"><?= $ottotal ?></td>
                    <td class="table-flat-data" <?= $right ?>>
                        <button class="button-solid round" id="action-button-info-icon" title="Edit"></button>
                        <button class="button-flashing round" id="action-button-delete-icon-white" title="Delete"></button>
                    </td>
                </tr>
                <?php }} else {?>
                <tr id="add-btn-row">
                    <td class="table-flat-data" colspan="5" style="background:rgb(50,50,50)">No Overtime Entry Found</td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <div class="form-flat" style="display:block; flex-flow:none;">
        <div id="top" style="width:100%;">
            <label for="" id="form-label2" style="color:rgb(0, 81, 255); font-size:20px"><b>Enter Overtime Entries</b></label>
            <button class="button-solid round" id="btn-load" 
                    style="float:right; 
                            background-color:rgb(43, 117, 255); 
                            font-size:16px;"
                    onclick="submitOvertime()">Submit</button>
        </div>
        <div id="bottom" style="width:auto; margin:10px 0px; padding:10px 20px">
            <table class="table-flat" id="overtime-table">
                <tr>
                    <th class="table-flat-header round-top-left">OVERTIME DATE</th>
                    <th class="table-flat-header">START TIME</th>
                    <th class="table-flat-header">END TIME</th>
                    <th class="table-flat-header">TOTAL RENDERED TIME</th>
                    <th class="table-flat-header round-top-right" style="width:40px"></th>
                </tr>
                <tr id="add-btn-row">
                    <td class="table-flat-data" id="add-entry-btn" colspan="5" onclick="addOvertimeEntry()"><b>+</b> Insert New Overtime Entry</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/overtime-add-function.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/overtime-layout.js"></script>
<style>
    #add-entry-btn {
        cursor:pointer; 
        border-bottom-left-radius:20px; 
        border-bottom-right-radius:20px;
        transition-duration:0.3s;
    }
    #add-entry-btn:hover {
        background-color: rgb(50,50,50);
    }
</style>