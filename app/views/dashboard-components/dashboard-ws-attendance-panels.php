<!-- For Working Scholars' Virtual Biometrics -->
<div class="form-flat">
    <div id="top-panel" style="width: 100%">
        <div id="attendance-button-panel">
            <div style="font-size:20px; padding-top: 10px; width:100%; height:auto; text-align:center; ">
                <b>VIRTUAL BIOMETRICS</b>
            </div>
            <button class="button-dashboard block" id="btn-submit-in" value="in">
                <div class="label-dash-btn">Submit Attendance</div>
                <span class="sub-dash-btn"><b>Time-In</b></span>
            </button>
            <button class="button-dashboard block" id="btn-submit-out" value="out">
                <div class="label-dash-btn">Submit Attendance</div>
                <span class="sub-dash-btn"><b>Time-Out</b></span>
            </button>
        </div>
        <b>
        <div id="my-schedules-panel" style="padding:15px 0px">
            <!-- Schedules List -->
            <div id="schedule-list" style="float:left; width:70%; height: 100%; margin: 0px 20px">
                <div id="panel-title">MY SCHEDULES FOR THIS SEMESTER: <span id="for-semester-name" style="color:rgb(52, 133, 255)"></span></div>
                <div id="panel-schedule-list" style="width:100%; height:180px; overflow-y: hidden; overflow-x: auto">
                    <div id="schedule-container" style="width: max-content; height: 100%;">
                        <!-- Using Schedule Panel -->
                    </div>
                </div>
            </div>
            <!-- Using Schedule Panel -->
            <div id="schedule-tab">
                <div id="schedule-label">Schedule for today</div>
                <div id="schedule-actual"><span id="for-day"></span><br><span id="for-time"></span></div>
            </div>
        </div>
        </b>
    </div>
    <div id="center-panel" style="width:100%; height:auto">
        <div id="my-attendance-panel">
            <div id="panel-title">MY ATTENDANCE FOR THIS MONTH: <span id="for-month-name" style="color:rgb(52, 133, 255)"></span></div>
            <div id="for-table-panel">
                <table class="table-flat" id="attendance-table">
                    <tr id="header-row">
                        <th class="table-flat-header round-top-left">Date</th>
                        <th class="table-flat-header">Time-In</th>
                        <th class="table-flat-header">Time-Out</th>
                        <th class="table-flat-header">Computed Late</th>
                        <th class="table-flat-header round-top-right">Computed Undertime</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="/uclm_scholarship/public/scripts/misc/dashboard-ws-functions.js"></script>