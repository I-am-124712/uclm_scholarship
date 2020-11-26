<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div class="drawer">

        <!-- Title -->
        <div id="title">
            DAILY TIME RECORD
        </div>
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
        <!-- DTR Month -->
        <label for="month" id="form-label2">Month</label>
        <select name="month" id="month" class="textbox-transparent">
        </select>
        <form action="" name="load-methods" id="load-methods">
            <label for="" class="form-label2" style="float: left; margin:10px 5px;">Schedule plotting method
                <label for="load-method" class="form-label2" style="float:left; margin-left: 20px; width:100%">
                    <input type="radio" name="load-method" id="auto" value="auto">
                    Automatic
                </label>
                <label for="load-method" class="form-label2" style="float:left; margin-left: 20px; width:100%">
                    <input type="radio" name="load-method" id="manual" value="manual">
                    Manual
                </label>
            </label>
        </form>
        <button class="button-solid round" id="btn-load" onclick="getDtrData()">Load Entries</button>
        <button class="button-solid round" id="btn-save" onclick="">Upload .dat / .csv File</button>
        <button class="button-solid round" id="btn-save" onclick="saveDtrData()">Save Changes</button>
        <button class="button-solid round" id="btn-pdf" onclick="generatePDF()">Generate PDF</button>
    </div>
    <div class="table"></div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/dtr-misc.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/dtr-functions.js"></script>