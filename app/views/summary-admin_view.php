<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat" style="width:auto;
                                    padding:20px;
                                    border-radius:20px;
                                    font-size:25px">
            <b>SUMMARY REPORT</b>
        </div>
        <div class="drawer">
            <!-- School Year -->
            <label for="" id="form-label2">School Year</label>
            <select name="school-year" id="school-year" class="textbox-transparent">
                <option value="2019-2020">2019-2020</option>
                <option value="2020-2021">2020-2021</option>
                <option value="2021-2022">2021-2022</option>
                <option value="2022-2023">2022-2023</option>
            </select>
            <!-- Department -->
            <label for="" id="form-label2">Department</label>
            <select name="department" id="department" class="textbox-transparent"></select>
            <!-- DTR period to load  -->
            <label for="period" id="form-label2">DTR Period</label>
            <select name="period" id="period" class="textbox-transparent">
                <option value="1" >First Period</option>
                <option value="2" >Second Period</option>
            </select>
            <!-- DTR Month -->
            <label for="month" id="form-label2">Month</label>
            <select name="month" id="month" class="textbox-transparent">
            </select>
            <button class="button-solid round" id="btn-load" onclick="saveSummary()">Confirm and Save</button>
            <button class="button-solid round" id="btn-pdf" onclick="releaseGeneratedSummary()">Release Summary</button>
            <button class="button-solid round" id="btn-pdf" onclick="generateSummaryPDF()">Generate PDF</button>
        </div>
        <div class="table">
            <div id="for-title-panel"></div>
            <div id="for-table-panel"></div>
        </div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/summary-admin-functions.js"></script>