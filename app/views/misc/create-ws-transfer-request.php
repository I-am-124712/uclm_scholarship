
<div class="form-flat" id="create">
    <div id="top" style="width:100%;">
        <label for="" id="form-label2" style="color:rgb(0, 81, 255); font-size:20px"><b>CREATE WS TRANSFER REQUEST</b></label>
        <textarea class="textbox-transparent" type="text" name="transfer-request-text" 
                id="transfer-request-text" placeholder="Write here"
                style="border:none;
                        background-color:white;
                        color:black;
                        width:calc(100% - 120px);
                        max-width:calc(100% - 120px);
                        min-width:calc(100% - 120px);
                        height:100px;
                        min-height:100px;
                        max-height:300px;
                        text-align:left;
                        overflow-wrap:normal;
                        overflow-y:scroll;
                        padding-left:15px;
                        font-size:1vw"></textarea>
        <button class="button-solid round" id="btn-load" 
                style="float:right; 
                        background-color:rgb(43, 117, 255); 
                        font-size:16px;"
                onclick="submitTransferRequest()">Submit</button>
    </div>
</div>
<script src="/uclm_scholarship/public/scripts/misc/ws-transfer-request-functions.js"></script>
<script>
</script>