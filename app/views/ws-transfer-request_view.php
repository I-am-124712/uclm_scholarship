<?php 
include 'user_view.php'; 

$finder = $args['finder'];

$status = $args['status'];
$labels = $args['labels'];
$requestStatus = $args['requestStatus'];
$result = $args['result'];

$allowedEdit = ($_SESSION['user_privilege'] == 999 || $_SESSION['user_privilege'] == 1);
?>

<div class="app-dash-panel">
    <div class="form-flat title">
        <b>WORKING SCHOLAR TRANSFER REQUESTS</b>
    </div>
        <div class="drawer" style="height:100%">
            <!-- School Year -->
            <form action="/uclm_scholarship/dash/ws/requests">
                <div id="drawer-title">FILTER REQUESTS</div>
                <select name="request_status" id="request_status" class="textbox-transparent" style="border:none; background:white;">
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
        </div>
        <div id="for-contents">
        <?php 
            if($_SESSION['user_privilege'] == 85)
                require 'misc/create-ws-transfer-request.php';
        ?>
            <div id="for-reloading">
                <?php
                    if(count($result) <= 0) { 
                ?>
                    <div class="form-flat"><b>No <?= $labels[$requestStatus] ?>Working Scholar Transfer Request Found</b></div>
                <?php 
                    } else {
                        foreach($result as $req) {
                            $requestId = $req->get('request_id');
                            $requestingUserId = $req->get('request_source_user_id');
                            $requestingUserName = implode(' ', [ $req->get('user_fname'), $req->get('user_lname') ]);
                            $requestMessage = $req->get('request_message');
                            $requestTimestamp = time_elapsed_string($req->get('request_timestamp')->format('Y-m-d h:i:s'));
                            $requestStatus = $req->get('request_status');
                            $requestingUserPhoto = $req->get('user_photo');
                            switch($requestStatus){
                                case 'APPROVED':
                                    $color = 'green';
                                    break;
                                case 'REJECTED':
                                    $color = 'red';
                                    break;
                                default:
                                    $color = 'black';
                            }
                ?>
                    <div class="form-flat" id=<?= $requestId?>>
                        <div id="descriptions" style="width:100%; height:auto;">
                            <!-- Location for WS Profile and Request ID -->
                            <div id="info-topbar" style="height:auto">
                                <!-- For WS Profile Pic and Name + Timestamp Panel -->
                                <div id="for-ws-info" style="width:100%">
                                    <div id="ws-pic" style="float:left; 
                                                            width:50px;
                                                            height:50px;
                                                            background-color:rgb(0, 81, 255); 
                                                            background-image:url('<?= $requestingUserPhoto ?>');
                                                            background-size:cover;
                                                            background-position:center;
                                                            background-repeat:no-repeat;
                                                            border-radius:100%;
                                                            margin: 0px 20px;">
                                    </div>
                                    <div id="ws-name-and-timestamp" style="float:left; padding:5px 0px">
                                        <span id="ws-name">
                                            <a href="" style="font-size:20px"><b><?= $requestingUserName ?></b></a>
                                        </span>
                                        <span style="margin-left:15px">
                                            Status: <span style="color:<?= $color ?>"><b><?= $requestStatus ?></b></span>
                                        </span>
                                        <br>
                                        <span id="timestamp" style="font-size:14px" value="<?= $requestTimestamp ?>"><?= $requestTimestamp ?></span>
                                    </div>
                                    <div id="for-request-id" style="float:right; margin-right:15px">
                                        <span id="request-id"><b>REQUEST ID: <?= $requestId?></b></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Location for request message -->
                        <div id="for-request-message" style="padding:15px 20px; width:100%; height:auto; font-size:18px">
                        <?= $requestMessage ?>
                        </div>
                    <?php if($allowedEdit && $requestStatus === 'PENDING') {?>
                        <div id="approve-reject-controls" 
                                style="margin:5px 20%; 
                                        padding:5px 0px;
                                        border-top:1px solid gray; 
                                        width:calc(80%); 
                                        height:auto;">
                            <button class="button-flashing round"
                                id="approve-request-btn"
                                style="width:calc(50% - 30px);
                                        height:30px;
                                        color:black;"
                                value=<?= $requestId ?>
                                onclick="decisionForRequest(this.value, 'APPROVED')">Approve Request</button>
                            <button class="button-flashing round"
                                id="reject-request-btn"
                                style="width:calc(50% - 30px);
                                        height:30px;
                                        float:right;
                                        color:black;"
                                value=<?= $requestId ?>
                                onclick="decisionForRequest(this.value, 'REJECTED')">Reject Request</button>
                        </div>
                    <?php } else if($requestStatus !== 'PENDING') {
                                $query = "SELECT [User].user_photo, [User].user_fname + ' ' + [User].user_lname AS 'user_name', 
                                    feedback_message, feedback_timestamp 
                                    FROM WSTransferFeedback
                                    INNER JOIN [User] ON [User].user_id = WSTransferFeedback.feedback_user_id
                                    WHERE request_id = ?;";
                                $bind = [ $requestId ]; 
                                
                                $result = ($finder->ready()->customSql($query)->setBindParams($bind)->result_set());

                                if(count($result) > 0){
                                    $feedback = $result[0];

                                    $feedbackUserPhoto = $feedback->get('user_photo');
                                    $feedbackSourceName = $feedback->get('user_name');
                                    $feedbackMessage = $feedback->get('feedback_message');
                                    $feedbackElapsed = time_elapsed_string($feedback->get('feedback_timestamp')->format('Y-m-d H:m:s'));
                                }
                    ?>
                        <div id="feedback-panel" 
                                style="margin:5px 20px; 
                                        padding:5px 0px;
                                        border-top:1px solid gray; 
                                        width:calc(100% - 40px); 
                                        height:auto;">
                            <div style="width:100%; font-size:20px; margin-bottom:15px; font-weight:bold; color:rgb(30, 98, 223);">ADMIN FEEDBACK:</div>
                            <div style="width:100%;">
                                <div>
                                    <div style="width:40px; 
                                                height:40px; 
                                                float:left; 
                                                border-radius:100%; 
                                                margin-right:10px;
                                                background-color:rgb(30, 98, 223);
                                                background-image:url(<?= $feedbackUserPhoto ?>);
                                                background-size:cover;
                                                background-repeat:no-repeat;
                                                background-position:center;"></div>
                                    <div id="feedback-info" style="padding:5px 0px; margin:0px 10px">
                                        <span style="font-size:18px; font-weight:bold; color:rgb(30, 98, 223);"><?= $feedbackSourceName ?></span><br>
                                        <span style="font-size:14px; font-weight:normal; color:gray"><?= $feedbackElapsed ?></span><br><br>
                                        <span style="font-size:18px; font-weight:normal; margin-left:20px; color:black"><?= $feedbackMessage ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php 
                        }
                    }
                ?>
            </div>
        </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/drawer-widget.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/ws-transfer-request-layout.js"></script>
<?php if($allowedEdit) { ?>
<script src="/uclm_scholarship/public/scripts/misc/ws-transfer-decision-function.js"></script>
<?php } ?>
<script>
    $('div#drawer-title').css({
        "font-size": "20px",
        "color":"rgb(30, 98, 223)",
        'font-weight':'bold',
    });

    idForUpdate = 'for-reloading';
    addForPerform(reloadPage, {id: idForUpdate, params: paramsForUpdate})
</script>