<style>
    #add-entry-btn {
        cursor:pointer; 
        border-bottom-left-radius:20px; 
        border-bottom-right-radius:20px;
        transition-duration:0.3s;
    }
    #add-entry-btn:hover {
        background-color: rgb(50,50,50);
    }
</style>
<div class="form-flat" style="display:block; flex-flow:none;">
    <div id="top" style="width:100%;">
        <label for="" id="form-label2" style="color:rgb(0, 81, 255); font-size:20px"><b>CREATE OVERTIME REQUEST</b></label>
        <input class="textbox-transparent" type="text" name="overtime-request-text" 
                id="overtime-request-text" placeholder="Write here"
                style="border:none;
                        background-color:white;
                        color:black;
                        width:calc(100% - 120px);
                        text-align:left;
                        overflow-wrap:normal;
                        padding-left:15px;
                        font-size:1vw">
        <button class="button-solid round" id="btn-load" 
                style="float:right; 
                        background-color:rgb(43, 117, 255); 
                        font-size:16px;"
                onclick="submitOvertimeRequest()">Submit</button>
    </div>
    <div id="bottom" style="width:auto; margin:10px 0px; padding:10px 20px">
        <table class="table-flat" id="overtime-request-table">
            <tr>
                <th class="table-flat-header round-top-left">OVERTIME DATE</th>
                <th class="table-flat-header">START TIME</th>
                <th class="table-flat-header">END TIME</th>
                <th class="table-flat-header">TOTAL RENDERED TIME</th>
                <th class="table-flat-header round-top-right" style="width:40px"></th>
            </tr>
            <tr id="add-btn-row">
                <td class="table-flat-data" id="add-entry-btn" colspan="5" onclick="addOvertimeEntry()"><b>+</b> Insert New Overtime Entry</td>
            </tr>
        </table>
    </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/overtime-layout.js"></script>