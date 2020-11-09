<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div class="drawer">
        <!-- Title -->
        <div id="title">
            MY DAILY TIME RECORD
        </div>
        <!-- School Year -->
        <label for="" id="form-label2">School Year</label>
        <select name="school-year" id="school-year" class="textbox-transparent">
            <option value="2019-2020">2019-2020</option>
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
        <button class="button-solid round" id="btn-load" onclick="getDtrData()">Load Entries</button>
    </div>
    <div class="table"></div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/dtr-style.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/dtr-ws-functions.js"></script>