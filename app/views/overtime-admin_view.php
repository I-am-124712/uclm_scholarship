<?php 

$requestStatus = isset($_GET['request_status'])? $_GET['request_status']:'all';

$status = [
    'all' => 'All Requests',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'pending' => 'Pending'
];

?>
<div class="app-dash-panel" id="dashboard-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat" style="width:auto;
                                    padding:20px;
                                    border-radius:20px;
                                    font-size:25px">
            <b>OVERTIME REQUESTS</b>
        </div>
        <div class="drawer">
            <form action="/uclm_scholarship/records/overtime" method="get">
                <!-- School Year -->
                <label for="" id="form-label2" style="color:rgb(0, 81, 255); font-size:20px"><b>FILTER REQUESTS</b></label>
                <select name="request_status" id="school-year" 
                        class="textbox-transparent"
                        style="border:none;
                            background-color:white;
                            box-shadow:3px 3px 3px rgba(0,0,0,0.1)">
                    <?php
                        foreach($status as $k => $v){
                            $selected = $requestStatus === $k ? 'selected':'';
                    ?>
                    <option value=<?= $k ?> <?= $selected ?>><?= $v ?></option>
                    <?php 
                        }
                    ?>
                </select>
                <button class="button-solid round" id="btn-load" type="submit">Load Entries</button>
            </form>
            <button class="button-solid round" id="btn-transparent" 
                    onclick="window.location.href='/uclm_scholarship/records/overtime/encode'">Encode Overtime</button>

            <?php if($_SESSION['user_privilege'] === 2) {?>
            <button class="button-solid round" id="btn-transparent" 
                    onclick="window.location.href='/uclm_scholarship/records/my_overtime'">My Overtime Requests</button>
            <?php } ?>

        </div>
        <div id="for-contents">
            <?php require 'misc/overtime-requests.php'; ?>
        </div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/drawer-widget.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/realtime-update-util.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/overtime-layout.js"></script>
<script> 
    idForUpdate = 'for-contents';
    addForPerform(reloadPage, {id: idForUpdate, params: paramsForUpdate}); 
</script>