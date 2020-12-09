

<div class="app-dash-panel" id="dashboard-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat" style="width:auto;
                                    padding:20px;
                                    border-radius:20px;
                                    font-size:25px">
            <b>OVERTIME</b>
        </div>
        <div class="drawer">
            <!-- School Year -->
            <label for="" id="form-label2">SCHOOL YEAR</label>
            <select name="school-year" id="school-year" class="textbox-transparent">
                <option value="2019-2020">2019-2020</option>
                <option value="2020-2021">2020-2021</option>
                <option value="2021-2022">2021-2022</option>
                <option value="2022-2023">2022-2023</option>
            </select>

            <!-- Selector for department to be used as basis for loading Overtimes -->
            <label for="department" id="form-label2">DEPARTMENT</label>
            <select name="department" id="department" class="textbox-transparent">
            </select>

            <!-- WS name to load -->
            <label for="working-scholar" id="form-label2">WORKING SCHOLAR</label>
            <select name="working-scholar" id="working-scholar" class="textbox-transparent">
            </select>
            <button class="button-solid round" 
                    id="btn-transparent" 
                    onclick=""
                    style="text-align:left;
                        font-size:14px;
                        font-weight:bold;
                        color:rgb(30, 98, 223);">
                OVERTIME REQUESTS
            </button>
            <button class="button-solid round" id="btn-load" onclick="getDtrDataWS()">Load Entries</button>
        </div>
        <div id="for-contents">
            <?php require 'misc/ws-overtime-profile.php'; ?>
        </div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/drawer-widget.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/overtime-admin-functions.js"></script>