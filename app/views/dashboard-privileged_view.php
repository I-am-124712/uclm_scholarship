<div style="width:auto; height:fit-content;">
    <!-- For dash button top panel -->
    <div class="form-flat" id="dash-btn-flat">
        <button class="button-dashboard">
            <div class="label-dash-btn">University Departments</div>
            <a class="sub-dash-btn" href="/uclm_scholarship/dash/departments"><b><?=isset($args['departmentCount'])? $args['departmentCount']:''?> Departments</b></a>
        </button>
        <button class="button-dashboard">
            <div class="label-dash-btn">Number of WS</div>
            <a class="sub-dash-btn" href="/uclm_scholarship/dash/ws/general?allow_edit"><b><?=isset($args['no_of_ws'])? $args['no_of_ws']:''?> WS</b></a>
        </button>
        <button class="button-dashboard">
            <div class="label-dash-btn">WS Notifications</div>
            <div class="sub-dash-btn"><b><?=isset($args['ws_with_ot'])? $args['ws_with_ot']:''?> Notifications</b></div>
        </button>
        <button class="button-dashboard">
            <div class="label-dash-btn">Reports</div>
            <div class="sub-dash-btn"><b><?=isset($args['ws_with_ot'])? $args['ws_with_ot']:''?> Reports</b></div>
        </button>
    </div>
    <div class="form-flat" id="chart-flat">
        <div class="chart-div-padded">
            <div id="chart-panel" style="height:100%; overflow-y: auto"></div>
        </div>
        <div class="chart-div-padded">
            <div id="chart-panel-2" style="height:100%; overflow-y: auto"></div>
        </div>
    </div>
    <div class="form-flat">
        <div class="chart-div">
            <div class="title">Messages</div>
            <div id="message-panel" style="height:250px; overflow-y: scroll"></div>
        </div>
        <div class="chart-div">
            <div id="summary-panel" style="height:100%; overflow-y: auto"></div>
        </div>
    </div>

    <div class="list-div">
    </div>

</div>
<script src="/uclm_scholarship/public/scripts/misc/dashboard-admin-functions.js"></script>