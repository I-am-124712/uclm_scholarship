<?php require 'user_view.php'; ?>

<div class="app-dash-panel" id="dashboard-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat" style="width:auto;
                                    padding:20px;
                                    border-radius:20px;
                                    font-size:25px">
            <b>MY OVERTIME REQUESTS</b>
        </div>
        <div class="drawer">
            <form action="/uclm_scholarship/records/my_overtime" method="get">
                <!-- School Year -->
                <label for="" id="form-label2" style="color:rgb(0, 81, 255); font-size:20px"><b>FILTER REQUESTS</b></label>
                <select name="request_status" id="school-year" 
                        class="textbox-transparent"
                        style="border:none;
                            background-color:white;
                            box-shadow:3px 3px 3px rgba(0,0,0,0.1)">
                    <option value="all">All Requests</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                </select>
                <button class="button-solid round" id="btn-load" type="submit">Load Entries</button>
            </form>
        </div>
        <div id="for-contents">
            <?php $_POST['my'] = true; require 'misc/ws-overtime-request.php';?>
            <div id="for-requests">
                <?php require 'misc/overtime-requests.php'; ?>
            </div>
        </div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/drawer-widget.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/realtime-update-util.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/overtime-ws-functions.js"></script>