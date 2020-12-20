<?php
    $data = $args['data'];
?>

<!-- For dash button top panel -->
<div class="form-flat" id="dash-btn-flat">
    <button class="button-dashboard flex">
        <div class="label-dash-btn">University Departments</div>
        <a class="sub-dash-btn" href="/uclm_scholarship/dash/departments"><b><?=isset($data['departmentCount'])? $data['departmentCount']:''?> Departments</b></a>
    </button>
    <button class="button-dashboard flex">
        <div class="label-dash-btn">Number of WS</div>
        <a class="sub-dash-btn" href="/uclm_scholarship/dash/ws/general?allow_edit"><b><?=isset($data['no_of_ws'])? $data['no_of_ws']:''?> WS</b></a>
    </button>
    <button class="button-dashboard flex">
        <div class="label-dash-btn">WS Notifications</div>
        <div class="sub-dash-btn"><b><?=isset($data['ws_with_ot'])? $data['ws_with_ot']:''?> Notifications</b></div>
    </button>
    <button class="button-dashboard flex">
        <div class="label-dash-btn">Reports & Printouts Released</div>
        <div class="sub-dash-btn"><b><?=isset($data['ws_with_ot'])? $data['ws_with_ot']:''?> Released Items</b></div>
    </button>
</div>