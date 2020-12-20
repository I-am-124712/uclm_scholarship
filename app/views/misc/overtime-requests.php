<?php
    if($_SESSION['user_privilege'] == 999 || $_SESSION['user_privilege'] == 1)
        $allowedApproval = true;
    else    
        $allowedApproval = false;

    $finder = $args['finderObj'];


    $whereClause = "";
    $statusName = "";

    if(isset($_GET['request_status'])){
        switch($_GET['request_status']){
            case 'approved':
                $statusName = "Approved";
                $whereClause = "WHERE OvertimeRequest.request_status = 'APPROVED'";
                break;
            case 'rejected':
                $statusName = "Rejected";
                $whereClause = "WHERE OvertimeRequest.request_status = 'REJECTED'";
                break;
            case 'pending':
                $statusName = "Pending";
                $whereClause = "WHERE OvertimeRequest.request_status = 'PENDING'";
                break;
            default:
                $whereClause = "WHERE 1=1";
        }
    }

    if(isset($_POST['my'])){
        $whereClause .= " AND OvertimeRequest.idnumber = " . str_replace('ws', '', $_SESSION['user_id']);
    }

    // determines the number of records to load in the page
    $loadOffset = isset($_POST['l_offset']) ? $_POST['l_offset']: 0;

    // to make sure "no strings attached" 😏
    if(gettype($loadOffset) != 'integer')
        $loadOffset = 0;

    // actual number of records to load.
    $numberOfRecordsToLoad = 10 + (5 * $loadOffset);    

    // Overtime Request view query.
    $sqlForOvertimeRequests = "SELECT TOP $numberOfRecordsToLoad request_id, ws.idnumber, ws.wsName, 
        request_message, [User].user_photo, request_timestamp, request_status
        FROM OvertimeRequest
        INNER JOIN WS on WS.idnumber = OvertimeRequest.idnumber
        INNER JOIN [User] on [User].user_id = WS.user_id 
        " . $whereClause . "
        ORDER BY OvertimeRequest.request_timestamp DESC;
    ";

    $res = $finder
    ->ready()
    ->customSql($sqlForOvertimeRequests)
    ->result_set();

    $overtimeRequests = [];

    if(count($res) > 0){
        foreach($res as $otRequest){
            $overtimeRecord = [];
            $overtimeRecord['requestId'] = $otRequest->get('request_id');
            $overtimeRecord['idnumber'] = $otRequest->get('idnumber');
            $overtimeRecord['userPhoto'] = $otRequest->get('user_photo');

            // Reformat our WS Name.
            $wsName = explode(", ", utf8_encode($otRequest->get('wsName')));
            $lastName = $wsName[0];
            $firstName = $wsName[1];
            $newWsName = $firstName . " " . $lastName;


            $overtimeRecord['wsName'] = $newWsName;
            $overtimeRecord['requestMessage'] = $otRequest->get('request_message');
            $overtimeRecord['requestStatus'] = $otRequest->get('request_status');
            $overtimeRecord['timestamp'] = $otRequest->get('request_timestamp');
            $overtimeRecord['overtimeRecords'] = [];
            $idnumber = $otRequest->get('idnumber');
            $requestId = $otRequest->get('request_id');
    
            $overtimeRecordsQuery = "SELECT otdate, ottimestart, ottimeend, ottotal 
                FROM Overtime
                INNER JOIN OvertimeRequestMapper on OvertimeRequestMapper.overtime_id = Overtime.overtime_id
                WHERE Overtime.idnumber = ? AND OvertimeRequestMapper.request_id = ?";
    
            $bindParams = [ $idnumber, $requestId ];
    
            $records = $finder->ready()
            ->customSql($overtimeRecordsQuery)
            ->setBindParams($bindParams)
            ->result_set();
    
            if(count($records)){
                foreach($records as $rec){
                    array_push($overtimeRecord['overtimeRecords'], $rec->get());
                }
            }
            array_push($overtimeRequests, $overtimeRecord);
        }
    }
?>
<?php 
if(!empty($overtimeRequests)){
    foreach($overtimeRequests as $req){
        $timestampString = $req['timestamp']->format('Y-m-d H:i:s');
        $agoString = time_elapsed_string($timestampString);

        switch($req['requestStatus']){
            case 'APPROVED':
                $color = "green";
                break;
            case 'REJECTED';
                $color = "red";
                break;
            default:
                $color = "black";
        }
?>

<div class="form-flat" id=<?= $req['requestId']?>>
    <div id="descriptions" style="width:100%; height:auto;">
        <!-- Location for WS Profile and Request ID -->
        <div id="info-topbar" style="height:auto">
            <!-- For WS Profile Pic and Name + Timestamp Panel -->
            <div id="for-ws-info">
                <div id="ws-pic" style="float:left; 
                                        width:50px;
                                        height:50px;
                                        background-color:rgb(0, 81, 255); 
                                        background-image:url('<?= $req['userPhoto'] ?>');
                                        background-size:cover;
                                        background-position:center;
                                        background-repeat:no-repeat;
                                        border-radius:100%;
                                        margin: 0px 20px;">
                </div>
                <div id="ws-name-and-timestamp" style="float:left; padding:5px 0px">
                    <span id="ws-name">
                        <a href="" style="font-size:20px"><b><?= $req['wsName'] ?></b></a>
                    </span>
                    <span style="margin-left:15px">
                        Status: <span style="color:<?= $color ?>"><b><?= $req['requestStatus'] ?></b></span>
                    </span>
                    <br>
                    <span id="timestamp" style="font-size:14px" value="<?= $timestampString ?>"><?= $agoString ?></span>
                </div>
                <div id="for-request-id" style="float:right; margin-right:15px">
                    <span id="request-id"><b>REQUEST ID: <?= $req['requestId']?></b></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Location for request message -->
    <div id="for-request-message" style="padding:10px 20px; width:100%; height:auto; font-size:18px">
    <?= $req['requestMessage'] ?>
    </div>
    <div id="overtime-table" style="padding:10px 20px; width:100%; height:auto;">
        <table class="table-flat">
            <tr>
                <th class="table-flat-header round-top-left">OVERTIME DATE</th>
                <th class="table-flat-header">START TIME</th>
                <th class="table-flat-header">END TIME</th>
                <th class="table-flat-header round-top-right">TOTAL RENDERED TIME</th>
            </tr>

            <?php 
                $i = 0;
                foreach($req['overtimeRecords'] as $overtimeRecord) { 
                    $overtimeDate = $overtimeRecord['otdate']->format('M d, Y');
                    $overtimeStart = $overtimeRecord['ottimestart']->format('h:i A');
                    $overtimeEnd = $overtimeRecord['ottimeend']->format('h:i A');
                    $overtimeTotal = $overtimeRecord['ottotal'];

                    if($i == (count($req['overtimeRecords']) - 1)){
                        $styleLeft = ' round-bottom-left';
                        $styleRight = ' round-bottom-right';
                    }else {
                        $styleLeft = '';
                        $styleRight = '';
                    }
            ?>
            <tr>
                <td class="table-flat-data<?= $styleLeft ?>"><?= $overtimeDate ?></td>
                <td class="table-flat-data"><?= $overtimeStart ?></td>
                <td class="table-flat-data"><?= $overtimeEnd ?></td>
                <td class="table-flat-data<?= $styleRight ?>"><?= $overtimeTotal ?></td>
            </tr>
            <?php 
                    ++$i;
                } ?>
        </table>
    </div>
    <?php if($allowedApproval && $req['requestStatus'] === 'PENDING'){ ?>
    
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
            value=<?= $req['requestId'] ?>
            onclick="decisionForRequest(this.value, 'APPROVED')">Approve Request</button>
        <button class="button-flashing round"
            id="reject-request-btn"
            style="width:calc(50% - 30px);
                    height:30px;
                    float:right;
                    color:black;"
            value=<?= $req['requestId'] ?>
            onclick="decisionForRequest(this.value, 'REJECTED')">Reject Request</button>
    </div>

    <?php } else if($req['requestStatus'] !== 'PENDING'){
                $query = "SELECT [User].user_photo, [User].user_fname + ' ' + [User].user_lname AS 'user_name', 
                    feedback_message, feedback_timestamp 
                    FROM OvertimeRequestFeedback
                    INNER JOIN [User] ON [User].user_id = OvertimeRequestFeedback.user_id
                    WHERE request_id = ?;";
                $bind = [ $req['requestId'] ]; 
                
                $result = ($finder->ready()->customSql($query)->setBindParams($bind)->result_set());

                if(count($result) > 0)
                    $feedback = $result[0];

                $feedbackUserPhoto = $feedback->get('user_photo');
                $feedbackSourceName = $feedback->get('user_name');
                $feedbackMessage = $feedback->get('feedback_message');
                $feedbackElapsed = time_elapsed_string($feedback->get('feedback_timestamp')->format('Y-m-d H:m:s'));
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

    <?php }?>
</div>

<?php
        }
    }else {
?>

<div class="form-flat">
<b>No <?= $statusName ?> Overtime Requests found</b>
</div>
<?php } 

if($allowedApproval){
?>
<script src="/uclm_scholarship/public/scripts/misc/overtime-requests-functions.js"></script>
<?php } ?>
