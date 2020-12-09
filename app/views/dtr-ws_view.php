<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat" style="width:auto;
                                    padding:20px;
                                    border-radius:20px;
                                    font-size:25px">
            <b>MY DAILY TIME RECORD</b>
            <div style="width:18px; 
                        height:18px; 
                        cursor:default;
                        font-family: 'Impact';
                        color:rgb(0, 162, 255); 
                        border: 2px solid rgb(0, 162, 255);
                        border-radius:100%;
                        margin:auto 5px;
                        font-size:14px;
                        text-align:center"
                title="This Information is for reference ONLY and is not considered official.">!</div>
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

            <!-- Selector for semester to be used as basis for schedule -->
            <label for="semester" id="form-label2">Use schedule for</label>
            <select name="semester" id="semester" class="textbox-transparent">
                <option value="1">First Semester</option>
                <option value="2">Second Semester</option>
                <option value="3">Summer</option>
            </select>

            <!-- checkbox for hiding or showing records with no times-in or -out -->
            
            <label for="hide" id="form-label2" style="margin: 10px 0px; float:left; width: 100%; font-size:12px">
                <input type="checkbox" name="hide" id="hide">
                Hide records with no in/out
            </label>

            <!-- DTR period to load  -->
            <label for="period" id="form-label2">DTR Period</label>
            <select name="period" id="period" class="textbox-transparent">
                <option value="1" >First Period</option>
                <option value="2" >Second Period</option>
            </select>
            <label for="month" id="form-label2">Month</label>
            <select name="month" id="month" class="textbox-transparent">
            </select>
            <button class="button-solid round" id="btn-load" onclick="getDtrDataWS()">Load Entries</button>
            <button class="button-solid round" id="btn-transparent" onclick="generatePDFWS()">Generate PDF</button>
        </div>
        <div class="table"></div>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/drawer-widget.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/dtr-misc.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/dtr-ws-functions.js"></script>